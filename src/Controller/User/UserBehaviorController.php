<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Attribute\Body;
use App\Controller\AbstractRestController;
use App\Entity\User;
use App\OptionsResolver\SettingOptionsResolver;
use App\Setting\SettingTemplate;
use App\ValueResolver\BodyResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UserBehaviorController extends AbstractRestController
{
    #[Route('/users/settings', name: 'create_update_setting', methods: ['POST'])]
    public function createOrUpdateSetting(
        EntityManagerInterface $em,
        SettingOptionsResolver $settingOptionsResolver,
        #[Body, ValueResolver(BodyResolver::class)] mixed $body,
        #[CurrentUser] User $user,
    ) {
        try {
            $data = $settingOptionsResolver
                ->configureName()
                ->configureValue()
                ->resolve($body);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $setting = SettingTemplate::getSetting($data['name']);

            $setting->setValue($data['value']);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $user->updateSetting($setting);

        $em->flush();

        return $this->json($user->getSettings());
    }
}
