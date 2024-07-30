<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\Entity\Setting;
use App\Entity\User;
use App\Exception\ApiException;
use App\OptionsResolver\SettingOptionsResolver;
use App\Service\RequestPayloadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/users/settings', name: 'create_update_setting', methods: ['POST'])]
    public function createOrUpdateSetting(
        EntityManagerInterface $em,
        RequestPayloadService $requestPayloadService,
        Request $request,
        SettingOptionsResolver $settingOptionsResolver
    ) {
        try {
            $body = $requestPayloadService->getRequestPayload($request);

            $data = $settingOptionsResolver
                ->configureName()
                ->configureValue()
                ->resolve($body);
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var User $user */
        $user = $this->getUser();

        $settings = new Setting();
        $settings
            ->setName($data['name'])
            ->setValue($data['value'])
            ->setUser($user);

        $user->updateSetting($settings);

        $em->flush();

        return $this->jsonStd($user->getSettings());
    }
}
