<?php

declare(strict_types=1);

namespace App\OptionsResolver;

use App\Entity\Session;
use App\Enum\GradeType;
use App\Repository\SessionRepository;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpacedRepetitionOptionsResolver extends OptionsResolver
{
    public function __construct(
        private SessionRepository $sessionRepository,
    ) {
    }

    public function configureGrade(): self
    {
        return $this
            ->setDefined('grade')
            ->setRequired('grade')
            ->setAllowedTypes('grade', 'int')
            ->setAllowedValues('grade', fn (int $grade) => GradeType::tryFrom($grade) !== null)
            ->setNormalizer('grade', function (Options $options, int $value) {
                return GradeType::tryFrom($value);
            });
    }

    public function configureSession(): self
    {
        return $this
            ->setDefined('session')
            ->setRequired('session')
            ->setAllowedTypes('session', 'int')
            ->setNormalizer('session', function (Options $options, $sessionId) {
                /** @var Session $session */
                $session = $this->sessionRepository->find($sessionId);

                if ($session === null) {
                    throw new InvalidOptionsException("Session with id {$sessionId} was not found");
                }

                return $session;
            });
    }
}
