<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Attribute\Body;
use App\Controller\AbstractRestController;
use App\Entity\PasswordReset;
use App\Exception\MaxTriesReachedException;
use App\Message\SendTextEmailMessage;
use App\OptionsResolver\PasswordResetOptionsResolver;
use App\OptionsResolver\UserOptionsResolver;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\UniqueGenerator\UniqueTokenGenerator;
use App\ValueResolver\BodyResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class PasswordForgottenController extends AbstractRestController
{
    #[Route('/reset-password/request', name: 'password_reset_request', methods: ['POST'])]
    public function requestPasswordReset(
        EntityManagerInterface $em,
        UserOptionsResolver $userOptionsResolver,
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        UniqueTokenGenerator $uniqueTokenGenerator,
        MessageBusInterface $messageBusInterface,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $userOptionsResolver
                ->configureIdentifier(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $associatedUser = $userRepository->loadUserByIdentifier($data['identifier']);

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

        // Temporarly create the element
        $passwordReset = new PasswordReset();
        $passwordReset
            ->setUsed(false)
            ->setUser($associatedUser)
            ->setToken($token);

        // Second validation using the validation constraints
        $this->validateEntity($passwordReset);

        // Save the new element
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
        PasswordResetOptionsResolver $passwordResetOptionsResolver,
        PasswordResetRepository $passwordResetRepository,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $passwordResetOptionsResolver
                ->configureToken(true)
                ->configurePassword(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $associatedPasswordResetRequest = $passwordResetRepository->findByToken($data['token']);

        if ($associatedPasswordResetRequest == null) {
            throw new BadRequestHttpException('No token found');
        }

        $associatedPasswordResetRequest->setUsed(true);

        $user = $associatedPasswordResetRequest->getUser();
        $user->setRawPassword($data['password']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $this->validateEntity($user, ['Default', 'edit:user:password']);

        $em->flush();

        return $this->json($user, Response::HTTP_OK, context: ['groups' => ['read:user:user']]);
    }
}
