<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\OptionsResolver\UserOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
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
        // TODO Add pagination
        $users = $userRepository->findAll();

        return $this->json($users);
    }

    #[Route('/users/{id}', name: 'get_user', methods: ['GET'])]
    public function getOneUser(User $user, UserRepository $userRepository): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserOptionsResolver $userOptionsResolver
    ): JsonResponse {

        $requestBody = json_decode($request->getContent(), true);

        $constraints = new Collection([
            'username' => new NotBlank(message: 'Test'),
        ]);

        // https://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
        // On passe les informations des erreurs via les status code
        //  De toute façon, il y aura aussi de la validation côté frontend

        try {
            $validator->validate($requestBody, $constraints);
        } catch (\Exception $e) {
            $this->createEr
        }

        // // Temporarly create the user
        // $user = new User();
        // $user
        //     ->setEmail($userDto->email)
        //     ->setUsername($userDto->username)
        //     ->setRoles($userDto->roles)
        //     ->setRawPassword($userDto->password);

        // $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));

        // // Second validation using the validation constraints
        // $errors = $validator->validate($user);
        // if (count($errors) > 0) {
        //     throw new ApiException(
        //         (string) $errors[0]->getMessage(),
        //         ExceptionCode::INVALID_REQUEST_PARAMETER,
        //         Response::HTTP_BAD_REQUEST
        //     );
        // }

        // // Save the new user
        // $em->persist($user);
        // $em->flush();

        // // Return the user with the the status 201 (Created)
        // return $this->json($user, status: Response::HTTP_CREATED);
    }
}
