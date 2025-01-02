<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\AbstractRestController;
use App\DTO\PasswordRequestDTO;
use App\DTO\PasswordResetDTO;
use App\Entity\PasswordReset;
use App\Exception\MaxTriesReachedException;
use App\Message\SendTextEmailMessage;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\UniqueGenerator\UniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth', 'api_auth_', format: 'json')]
class PasswordForgottenController extends AbstractRestController
{
    #[Route('/reset-password/request', name: 'password_reset_request', methods: ['POST'])]
    public function requestPasswordReset(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        UniqueTokenGenerator $uniqueTokenGenerator,
        MessageBusInterface $messageBusInterface,
    ): JsonResponse {
        $passwordRequest = $this->decodeBody(
            classname: PasswordRequestDTO::class,
            deserializationGroups: ['write:passwordReset:request'],
        );

        $associatedUser = $userRepository->loadUserByIdentifier($passwordRequest->identifier);

        if ($associatedUser == null) {
            throw new BadRequestHttpException("This email or username doesn't exist");
        }

        if ($passwordResetRepository->getLastRequest($associatedUser) !== null) {
            throw new BadRequestHttpException('A password reset request is already in progress. Please try again later');
        }

        try {
            $token = $uniqueTokenGenerator->generate(PasswordReset::class, 'token');
        } catch (MaxTriesReachedException $e) {
            throw new PreconditionFailedHttpException($e->getMessage(), $e);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured', $e);
        }

        $passwordReset = new PasswordReset();
        $passwordReset
            ->setUsed(false)
            ->setUser($associatedUser)
            ->setToken($token);

        $this->validateEntity($passwordReset);
        $em->persist($passwordReset);
        $em->flush();

        $messageBusInterface->dispatch(new SendTextEmailMessage(
            $associatedUser->getEmail(),
            $associatedUser->getUsername(),
            Email::PRIORITY_HIGH,
            'Password reset',
            $token
        ));

        return $this->json(null, Response::HTTP_CREATED);
    }

    #[Route('/reset-password/proceed', name: 'password_reset_proceed', methods: ['POST'])]
    public function checkToken(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        PasswordResetRepository $passwordResetRepository,
    ): JsonResponse {
        $passwordRequest = $this->decodeBody(
            classname: PasswordResetDTO::class,
            deserializationGroups: ['write:passwordReset:proceed'],
        );

        $associatedPasswordResetRequest = $passwordResetRepository->findByToken($passwordRequest->token);

        if ($associatedPasswordResetRequest == null) {
            throw new BadRequestHttpException('No token found');
        }

        $associatedPasswordResetRequest->setUsed(true);

        $user = $associatedPasswordResetRequest->getUser();
        $user->setRawPassword($passwordRequest->rawPassword);
        $user->setPassword($passwordHasher->hashPassword($user, $passwordRequest->rawPassword));

        $this->validateEntity($user, ['Default', 'edit:user:password']);
        $em->flush();

        return $this->json($user, Response::HTTP_OK, context: ['groups' => ['read:user:user']]);
    }
}
