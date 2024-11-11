<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Attribute\Body;
use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Exception\Http\UnauthorizedHttpException;
use App\OptionsResolver\UserOptionsResolver;
use App\UniqueGenerator\UniqueTokenGenerator;
use App\Utility\Roles;
use App\ValueResolver\BodyResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth', 'api_auth_', format: 'json')]
class AuthenticationController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        if ($user === null) {
            throw new UnauthorizedHttpException('Unauthenticated user');
        }

        return $this->jsonStd($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserOptionsResolver $userOptionsResolver,
        UniqueTokenGenerator $uniqueTokenGenerator,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
    ): JsonResponse {
        try {
            // Validate the content of the request body
            $data = $userOptionsResolver
                ->configureUsername(true)
                ->configureEmail(true)
                ->configurePassword(true)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        // Temporarly create the element
        $user = new User();
        $user
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setToken($uniqueTokenGenerator->generate(User::class, 'token'))
            ->setRoles([Roles::User])
            ->setRawPassword($data['password']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Second validation using the validation constraints
        $this->validateEntity($user, ['Default', 'edit:user:password']);

        // Save the new element
        $em->persist($user);
        $em->flush();

        return $this->jsonStd($user, Response::HTTP_CREATED, context: ['groups' => ['read:user:user']]);
    }
}
