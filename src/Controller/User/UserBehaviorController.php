<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractRestController;
use App\DTO\SettingDTO;
use App\Entity\User;
use App\Enum\SettingName;
use App\Modifier\Modifier;
use App\Modifier\Transformer\EnumTransformer;
use App\Setting\SettingTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', 'api_', format: 'json')]
class UserBehaviorController extends AbstractRestController
{
    #[Route('/users/settings', name: 'create_update_setting', methods: ['POST'])]
    public function createOrUpdateSetting(
        EntityManagerInterface $em,
        #[CurrentUser] User $user,
    ) {
        $setting = $this->decodeBody(
            classname: SettingDTO::class,
            deserializationGroups: ['write:setting:user'],
            transformers: [
                new Modifier('name', EnumTransformer::class, ['enum' => SettingName::class]),
            ]
        );

        try {
            $defaultSetting = SettingTemplate::getSetting($setting->name);
            $defaultSetting->setValue($setting->value);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $user->updateSetting($defaultSetting);
        $em->flush();

        return $this->json($user->getSettings());
    }
}
