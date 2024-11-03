<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\JsonStandardStatus;
use App\Enum\PeriodType;
use App\Enum\SettingName;
use App\Exception\ApiException;
use App\Model\JsonStandard;
use App\OptionsResolver\CriteriaOptionsResolver;
use App\OptionsResolver\PeriodOptionsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private CriteriaOptionsResolver $criteriaOptionsResolver,
        private PeriodOptionsResolver $periodOptionsResolver,
    ) {
    }

    /**
     * @template T
     *
     * @param Request $request The HTTP request
     * @param class-string<T> $criteriaEnum Fully qualified class CriteriaOptionsResolver (FQCN) of the enum CriteriaOptionsResolver. The enum CriteriaOptionsResolver should implement string-backed cases.
     * @param string $defaultValue The default value for the 'criteria' option
     *
     * @return T The resolved criteria from the enum
     */
    public function getCountCriteria(Request $request, string $criteriaEnum, string $defaultValue): mixed
    {
        try {
            $data = $this->criteriaOptionsResolver
                ->configureCriteria($criteriaEnum, $defaultValue)
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var T $criteria */
        $criteria = $data['criteria'];

        return $criteria;
    }

    public function getPeriodParameter(Request $request): PeriodType
    {
        try {
            $data = $this->periodOptionsResolver
                ->configurePeriod()
                ->setIgnoreUndefined()
                ->resolve($request->query->all());
        } catch (\Exception $e) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }

        /** @var PeriodType $period */
        $period = $data['period'];

        return $period;
    }

    public function validateEntity(mixed $entity, array $validationGroups = ['Default']): void
    {
        $errors = $this->validator->validate($entity, groups: $validationGroups);
        if (\count($errors) > 0) {
            throw new ApiException(Response::HTTP_BAD_REQUEST, (string) $errors[0]->getMessage());
        }
    }

    public function jsonStd(mixed $data, int $status = 200, array $headers = [], array $context = [], JsonStandardStatus $jsonStatus = JsonStandardStatus::VALID): JsonResponse
    {
        return $this->json(new JsonStandard($data, $jsonStatus), $status, $headers, $context);
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
