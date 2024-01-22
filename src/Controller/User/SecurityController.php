<?php

namespace App\Controller\User;

use Exception;
use App\Entity\User;
use App\Exception\ApiException;
use App\Service\TokenGenerator;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\AbstractRestController;
use App\OptionsResolver\UserOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class SecurityController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException(Response::HTTP_UNAUTHORIZED, 'Unauthenticated user');
        }

        return $this->json($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserOptionsResolver $userOptionsResolver,
        RequestPayloadService $requestPayloadService,
        TokenGenerator $tokenGenerator
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
        } catch (Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        // Temporarly create the element
        $user = new User();
        $user
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setToken($tokenGenerator->generateToken())
            ->setRawPassword($data['password']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Second validation using the validation constraints
        $this->validateEntity($user, ['Default', 'edit:user:password']);

        // Save the new element
        $em->persist($user);
        $em->flush();

        return $this->json($user, Response::HTTP_CREATED, context: ['groups' => ['read:user:user']]);
    }
}
