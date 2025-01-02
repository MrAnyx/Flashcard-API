<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Exception\Http\UnauthorizedHttpException;
use App\Modifier\Modifier;
use App\Modifier\Mutator\HashPasswordMutator;
use App\UniqueGenerator\UniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/auth', 'api_auth_', format: 'json')]
class AuthenticationController extends AbstractRestController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        if ($user === null) {
            throw new UnauthorizedHttpException('Unauthenticated user');
        }

        return $this->json($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        EntityManagerInterface $em,
        UniqueTokenGenerator $uniqueTokenGenerator,
    ): JsonResponse {
        $user = $this->decodeBody(
            classname: User::class,
            deserializationGroups: ['write:user:user'],
            mutators: [
                new Modifier('rawPassword', HashPasswordMutator::class),
            ],
            validationGroups: null
        );

        $user->setToken($uniqueTokenGenerator->generate(User::class, 'token'));

        $this->validateEntity($user, ['Default', 'edit:user:password']);

        $em->persist($user);
        $em->flush();

        return $this->json($user, Response::HTTP_CREATED, context: ['groups' => ['read:user:user']]);
    }
}
