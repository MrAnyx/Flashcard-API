<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Setting;
use App\Entity\User;
use App\Exception\ApiException;
use App\OptionsResolver\SettingOptionsResolver;
use App\OptionsResolver\UserOptionsResolver;
use App\Setting\SettingFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', 'api_', format: 'json')]
class UserController extends AbstractRestController
{
    #[Route('/users/me', name: 'get_me', methods: ['GET'])]
    public function getMe()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->jsonStd($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/users/me', name: 'delete_me', methods: ['DELETE'])]
    public function deleteMe(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        /*
        Delete all children using orphanRemove :
        - Flashcard
        - Unit
        - Topic
        - Review
        - Setting
        */
        $em->remove($user);
        $em->flush();

        return $this->jsonStd(null, status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/me', name: 'update_me', methods: ['PATCH', 'PUT'])]
    public function updateUser(
        EntityManagerInterface $em,
        Request $request,
        UserOptionsResolver $userOptionsResolver,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Retrieve the request body
        $body = $this->getRequestPayload($request);

        try {
            // Check if the request method is PUT. In this case, all parameters must be provided in the request body.
            // Otherwise, all parameters are optional.
            $mandatoryParameters = $request->getMethod() === 'PUT';

            // Validate the content of the request body
            $data = $userOptionsResolver
                ->configureUsername($mandatoryParameters)
                ->configureEmail($mandatoryParameters)
                ->configurePassword($mandatoryParameters)
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var User $user */
        $user = $this->getUser();

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
                    $validationGroups[] = 'edit:user:password';
                    break;
            }
        }

        // Second validation using the validation constraints
        $this->validateEntity($user, $validationGroups);

        // Save the user information
        $em->flush();

        // Return the user
        return $this->jsonStd($user, context: ['groups' => ['read:user:user']]);
    }

    #[Route('/users/settings', name: 'create_update_setting', methods: ['POST'])]
    public function createOrUpdateSetting(
        EntityManagerInterface $em,
        Request $request,
        SettingOptionsResolver $settingOptionsResolver
    ) {
        $body = $this->getRequestPayload($request);

        try {
            $data = $settingOptionsResolver
                ->configureName()
                ->configureValue()
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var User $user */
        $user = $this->getUser();

        try {
            $setting = SettingFactory::create($data['name'], $data['value']);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        $user->updateSetting($setting);

        $em->flush();

        return $this->jsonStd($user->getSettings());
    }
}
