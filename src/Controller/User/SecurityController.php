<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\PasswordReset;
use App\Entity\User;
use App\Exception\ApiException;
use App\Exception\MaxTriesReachedException;
use App\Message\SendTextEmailMessage;
use App\OptionsResolver\PasswordResetOptionsResolver;
use App\OptionsResolver\UserOptionsResolver;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\Service\RequestPayloadService;
use App\UniqueGenerator\UniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException(Response::HTTP_UNAUTHORIZED, 'Unauthenticated user');
        }

        return $this->jsonStd($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserOptionsResolver $userOptionsResolver,
        RequestPayloadService $requestPayloadService,
        UniqueTokenGenerator $uniqueTokenGenerator
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $userOptionsResolver
                ->configureUsername(true)
                ->configureEmail(true)
                ->configurePassword(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Temporarly create the element
        $user = new User();
        $user
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setToken($uniqueTokenGenerator->generate(User::class, 'token'))
            ->setRoles(['ROLE_USER'])
            ->setRawPassword($data['password']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Second validation using the validation constraints
        $this->validateEntity($user, ['Default', 'edit:user:password']);

        // Save the new element
        $em->persist($user);
        $em->flush();

        return $this->jsonStd($user, Response::HTTP_CREATED, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/reset-password/request', name: 'password_reset_request', methods: ['POST'])]
    public function requestPasswordReset(
        Request $request,
        EntityManagerInterface $em,
        UserOptionsResolver $userOptionsResolver,
        RequestPayloadService $requestPayloadService,
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        UniqueTokenGenerator $uniqueTokenGenerator,
        MessageBusInterface $messageBusInterface
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $userOptionsResolver
                ->configureEmail(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $associatedUser = $userRepository->findOneBy(['email' => $data['email']]);

        if ($associatedUser == null) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'No user was found with this email');
        }

        if ($passwordResetRepository->getLastRequest($associatedUser) !== null) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'A password reset request is already in progress. Please try again later');
        }

        try {
            $token = $uniqueTokenGenerator->generate(PasswordReset::class, 'token');
        } catch (MaxTriesReachedException $e) {
            throw new ApiException(Response::HTTP_PRECONDITION_FAILED, $e->getMessage());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'An error occured');
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

        return $this->jsonStd(null, Response::HTTP_CREATED);
    }

    #[Route('/reset-password/proceed', name: 'password_reset_proceed', methods: ['POST'])]
    public function checkToken(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        RequestPayloadService $requestPayloadService,
        PasswordResetOptionsResolver $passwordResetOptionsResolver,
        PasswordResetRepository $passwordResetRepository,
    ): JsonResponse {
        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $passwordResetOptionsResolver
                ->configureToken(true)
                ->configurePassword(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $associatedPasswordResetRequest = $passwordResetRepository->findByToken($data['token']);

        if ($associatedPasswordResetRequest == null) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'No token found');
        }

        $associatedPasswordResetRequest->setUsed(true);

        $user = $associatedPasswordResetRequest->getUser();
        $user->setRawPassword($data['password']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $this->validateEntity($user, ['Default', 'edit:user:password']);

        $em->flush();

        return $this->jsonStd($user, Response::HTTP_OK, context: ['groups' => ['read:user:user']]);
    }
}
