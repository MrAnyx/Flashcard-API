<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractRestController;
use App\Entity\User;
use App\Exception\ApiException;
use App\OptionsResolver\UserOptionsResolver;
use App\Repository\UserRepository;
use App\UniqueGenerator\UniqueTokenGenerator;
use App\Utility\Regex;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin', 'api_admin_', format: 'json')]
class UserAdminController extends AbstractRestController
{
    // #[Route('/users', name: 'get_users', methods: ['GET'])]
    // public function getAllUsers(
    //     Request $request,
    //     UserRepository $userRepository,
    // ): JsonResponse {
    //     $pagination = $this->getPaginationParameter(User::class, $request);
    //     $filter = $this->getFilterParameter(User::class, $request);

    //     // Get data with pagination
    //     $users = $userRepository->paginateAndFilterAll($pagination, $filter);

    //     // Return paginate data
    //     return $this->jsonStd($users, context: ['groups' => ['read:user:admin']]);
    // }

    // #[Route('/users/{id}', name: 'get_user', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    // public function getOneUser(int $id, UserRepository $userRepository): JsonResponse
    // {
    //     // Retrieve the element by id
    //     $user = $userRepository->find($id);

    //     // Check if the element exists
    //     if ($user === null) {
    //         throw new ApiException(Response::HTTP_NOT_FOUND, 'User with id %d was not found', [$id]);
    //     }

    //     return $this->jsonStd($user, context: ['groups' => ['read:user:admin']]);
    // }

    // #[Route('/users', name: 'create_user', methods: ['POST'])]
    // public function createUser(
    //     Request $request,
    //     EntityManagerInterface $em,
    //     UserPasswordHasherInterface $passwordHasher,
    //     UserOptionsResolver $userOptionsResolver,
    //     UniqueTokenGenerator $uniqueTokenGenerator,
    // ): JsonResponse {
    //     // Retrieve the request body
    //     $body = $this->getRequestPayload($request);

    //     try {
    //         // Validate the content of the request body
    //         $data = $userOptionsResolver
    //             ->configureUsername(true)
    //             ->configureEmail(true)
    //             ->configurePassword(true)
    //             ->configureRoles(true)
    //             ->resolve($body);
    //     } catch (\Exception $e) {
    //         throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
    //     }

    //     // Temporarly create the element
    //     $user = new User();
    //     $user
    //         ->setEmail($data['email'])
    //         ->setUsername($data['username'])
    //         ->setToken($uniqueTokenGenerator->generate(User::class, 'token'))
    //         ->setRoles($data['roles'])
    //         ->setRawPassword($data['password']);

    //     $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

    //     // Second validation using the validation constraints
    //     $this->validateEntity($user, ['Default', 'edit:user:password']);

    //     // Save the new element
    //     $em->persist($user);
    //     $em->flush();

    //     // Return the element with the the status 201 (Created)
    //     return $this->jsonStd(
    //         $user,
    //         Response::HTTP_CREATED,
    //         ['Location' => $this->generateUrl('api_admin_get_user', ['id' => $user->getId()])],
    //         ['groups' => ['read:user:admin']]
    //     );
    // }

    // #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    // public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    // {
    //     // Retrieve the user by id
    //     $user = $userRepository->find($id);

    //     // Check if the user exists
    //     if ($user === null) {
    //         throw new ApiException(Response::HTTP_NOT_FOUND, 'User with id %d was not found', [$id]);
    //     }

    //     // Remove the  user
    //     $em->remove($user);
    //     $em->flush();

    //     // Return a response with status 204 (No Content)
    //     return $this->jsonStd(null, Response::HTTP_NO_CONTENT);
    // }

    // #[Route('/users/{id}', name: 'update_user', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    // public function updateUser(
    //     int $id,
    //     UserRepository $userRepository,
    //     EntityManagerInterface $em,
    //     Request $request,
    //     UserOptionsResolver $userOptionsResolver,
    //     UserPasswordHasherInterface $passwordHasher,
    // ): JsonResponse {
    //     // Retrieve the user by id
    //     $user = $userRepository->find($id);

    //     // Check if the user exists
    //     if ($user === null) {
    //         throw new ApiException(Response::HTTP_NOT_FOUND, 'User with id %d was not found', [$id]);
    //     }

    //     // Retrieve the request body
    //     $body = $this->getRequestPayload($request);

    //     try {
    //         // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
    //         // Otherwise, all parameters are optional.
    //         $mandatoryParameters = $request->getMethod() === 'PUT';

    //         // Validate the content of the request body
    //         $data = $userOptionsResolver
    //             ->configureUsername($mandatoryParameters)
    //             ->configureEmail($mandatoryParameters)
    //             ->configurePassword($mandatoryParameters)
    //             ->configureRoles($mandatoryParameters)
    //             ->resolve($body);
    //     } catch (\Exception $e) {
    //         throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
    //     }

    //     $validationGroups = ['Default'];

    //     // Update each fields if necessary
    //     foreach ($data as $field => $value) {
    //         switch ($field) {
    //             case 'username':
    //                 $user->setUsername($value);
    //                 break;
    //             case 'email':
    //                 $user->setEmail($value);
    //                 break;
    //             case 'password':
    //                 $user->setRawPassword($value);
    //                 $user->setPassword($passwordHasher->hashPassword($user, $value));
    //                 $validationGroups[] = 'edit:user:password';
    //                 break;
    //             case 'roles':
    //                 $user->setRoles($value);
    //                 break;
    //         }
    //     }

    //     // Second validation using the validation constraints
    //     $this->validateEntity($user, $validationGroups);

    //     // Save the user information
    //     $em->flush();

    //     // Return the user
    //     return $this->jsonStd($user, context: ['groups' => ['read:user:admin']]);
    // }
}
