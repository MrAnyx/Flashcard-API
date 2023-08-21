<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Utility\Regex;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use App\OptionsResolver\UserOptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', 'api_', format: 'json')]
#[IsGranted('IS_AUTHENTICATED', message: 'Access denied: You can access this ressource')]
class UserController extends AbstractController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe()
    {
        $user = $this->getUser();

        if ($user === null) {
            throw new ApiException('You must login befor using this route', Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($user);
    }

    #[Route('/users', name: 'get_users', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access denied: You can access this ressource')]
    public function getAllUsers(
        Request $request,
        UserRepository $userRepository,
        PaginatorOptionsResolver $paginatorOptionsResolver
    ): JsonResponse {

        // Retrieve pagination parameters
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                ->resolve($request->query->all());
        } catch (Exception $e) {
            throw new ApiException($e->getMessage());
        }

        // Get data with pagination
        $users = $userRepository->findAllWithPagination($queryParams['page']);

        // Return paginate data
        return $this->json($users);
    }

    #[Route('/users/{id}', name: 'get_user', methods: ['GET'], requirements: ['id' => Regex::INTEGER])]
    #[IsGranted('ROLE_ADMIN', message: 'Access denied: You can access this ressource')]
    public function getOneUser(int $id, UserRepository $userRepository): JsonResponse
    {
        // Retrieve the element by id
        $user = $userRepository->find($id);

        // Check if the element exists
        if ($user === null) {
            throw new ApiException("User with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        return $this->json($user);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access denied: You can access this ressource')]
    public function createUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        UserOptionsResolver $userOptionsResolver,
        RequestPayloadService $requestPayloadService
    ): JsonResponse {

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Validate the content of the request body
            $data = $userOptionsResolver->configureAll(true)->resolve($body);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Temporarly create the element
        $user = new User();
        $user
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setRoles($data['roles'])
            ->setRawPassword($data['password']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Second validation using the validation constraints
        $errors = $validator->validate($user, groups: ['Default', 'update:password']);
        if (count($errors) > 0) {
            throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Save the new element
        $em->persist($user);
        $em->flush();

        // Return the element with the the status 201 (Created)
        return $this->json($user, Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('api_get_user', ['id' => $user->getId()]),
        ]);
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'], requirements: ['id' => Regex::INTEGER])]
    #[IsGranted('ROLE_ADMIN', message: 'Access denied: You can access this ressource')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the user by id
        $user = $userRepository->find($id);

        // Check if the user exists
        if ($user === null) {
            throw new ApiException("User with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        // Check if the user isn't the current user
        if ($user === $this->getUser()) {
            throw new ApiException('You can not delete your own user', Response::HTTP_FORBIDDEN);
        }

        // Remove the  user
        $em->remove($user);
        $em->flush();

        // Return a response with status 204 (No Content)
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'update_user', methods: ['PATCH', 'PUT'], requirements: ['id' => Regex::INTEGER])]
    #[IsGranted('ROLE_ADMIN', message: 'Access denied: You can access this ressource')]
    public function updateUser(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        UserOptionsResolver $userOptionsResolver,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {

        // Retrieve the user by id
        $user = $userRepository->find($id);

        // Check if the user exists
        if ($user === null) {
            throw new ApiException("User with id $id was not found", Response::HTTP_NOT_FOUND);
        }

        try {
            // Retrieve the request body
            $body = $requestPayloadService->getRequestPayload($request);

            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $userOptionsResolver->configureAll($mandatoryParameters)->resolve($body);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $validationGroups = ['Default'];

        // Update each fields if necessary
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'username':
                    $user->setUsername($value);
                    break;
                case 'email':
                    $user->setEmail($value);
                    break;
                case 'password':
                    $user->setRawPassword($value);
                    $user->setPassword($passwordHasher->hashPassword($user, $value));
                    $validationGroups[] = 'update:password';
                    break;
                case 'roles':
                    $user->setRoles($value);
                    break;
            }
        }

        // Second validation using the validation constraints
        $errors = $validator->validate($user, groups: $validationGroups);
        if (count($errors) > 0) {
            throw new ApiException((string) $errors[0]->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        // Save the user information
        $em->flush();

        // Return the user
        return $this->json($user);
    }
}
