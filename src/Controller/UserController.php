<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\OptionsResolver\UserOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\InvalidRequestParameterException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', 'api_', format: 'json')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json($users);
    }

    #[Route('/users/{id}', name: 'get_user', methods: ['GET'])]
    public function getOneUser(User $user, UserRepository $userRepository): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserOptionsResolver $userOptionsResolver): JsonResponse
    {
        $params = json_decode($request->getContent(), true);

        try {
            $fields = $userOptionsResolver
                ->configureEmail(true)
                ->configureUsername(true)
                ->configurePassword(true)
                ->configureRoles(true)
                ->resolve($params);
        } catch (\Exception $e) {
            throw new InvalidRequestParameterException($e->getMessage());
        }

        $user = new User();
        $user
            ->setEmail($fields['email'])
            ->setUsername($fields['username'])
            ->setRawPassword($fields['password']);

        $user->setPassword($passwordHasher->hashPassword($user, $fields['password']));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            throw new InvalidRequestParameterException((string) $errors[0]->getMessage());
        }

        $em->persist($user);
        $em->flush();

        return $this->json($user, status: Response::HTTP_CREATED);
    }
}
