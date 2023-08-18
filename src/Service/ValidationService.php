<?php

namespace App\Service;

use Exception;
use App\DTO\DTO;
use InvalidArgumentException;
use App\Exception\ApiException;
use App\Exception\ExceptionStatus;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate(DTO $object, string $message = null): void
    {
        $errors = $this->validator->validate($object, $object->getValidationRules());
        if (count($errors) > 0) {
            throw new InvalidArgumentException($message ?? (string) $errors[0]->getMessage());
        }
    }

    /**
     * @throws ApiException
     */
    public function validateOrThrow(DTO $object, ExceptionStatus $status, string $message = null): void
    {
        try {
            $this->validate($object, $message);
        } catch (InvalidArgumentException $e) {
            throw new ApiException($e->getMessage(), $status);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ExceptionStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
