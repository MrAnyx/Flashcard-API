<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\PeriodType;
use App\Enum\SettingName;
use App\OptionsResolver\PeriodOptionsResolver;
use App\Service\RequestDecoder;
use App\Transformer\Transformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractRestController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly PeriodOptionsResolver $periodOptionsResolver,
        private readonly RequestDecoder $requestDecoder,
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

    /**
     * @template T
     *
     * @param class-string<T> $classname
     * @param bool|null $strict Define the strictness of the field resolution. With value null, the strictness with be guessed by the request method (POST and PUT)
     * @param array<string, Transformer[]> $transformers
     * @param array<string, Transformer[]> $mutators
     *
     * @return T
     */
    public function decodeBody(
        string $classname,
        ?object $fromObject = null,
        ?bool $strict = null,
        array $deserializationGroups = [],
        bool $ignoreUnknownFields = true,
        array $transformers = [],
        array $mutators = [],
        ?array $validationGroups = ['Default'],
    ) {
        try {
            return $this->requestDecoder->decode($classname, $fromObject, $strict, $deserializationGroups, $ignoreUnknownFields, $transformers, $mutators, $validationGroups);
        } catch (\Exception $ex) {
            throw new BadRequestHttpException($ex->getMessage(), $ex);
        }
    }
}
