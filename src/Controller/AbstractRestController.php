<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\PeriodType;
use App\Enum\SettingName;
use App\OptionsResolver\PeriodOptionsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private PeriodOptionsResolver $periodOptionsResolver,
    ) {
    }

    public function getPeriodParameter(Request $request): PeriodType
    {
        try {
            $data = $this->periodOptionsResolver
                ->configurePeriod()
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        /** @var PeriodType $period */
        $period = $data['period'];

        return $period;
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (\count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors[0]->getMessage());
        }
    }

    public function getUserSetting(SettingName $settingName): mixed
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $user->getSetting($settingName);
    }
}
