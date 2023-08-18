<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        RequestPayloadService $requestPayloadService
    ): JsonResponse {

        $userDTO = $requestPayloadService->getRequestPayload($request, UserDTO::class);

        dd($userDTO);

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
