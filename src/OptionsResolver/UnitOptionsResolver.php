<?php

namespace App\OptionsResolver;

use App\Repository\TopicRepository;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class UnitOptionsResolver extends OptionsResolver
{
    public function __construct(
        private TopicRepository $topicRepository
    ) {
    }

    public function configureName(bool $isRequired = true): self
    {
        $this->setDefined('name')->setAllowedTypes('name', 'string');

        if ($isRequired) {
            $this->setRequired('name');
        }

        return $this;
    }

    public function configureTopic(bool $isRequired = true): self
    {
        $this->setDefined('topic')->setAllowedTypes('topic', 'int');

        if ($isRequired) {
            $this->setRequired('topic');
        }

        $this->setNormalizer('topic', function (Options $options, $value) {
            $topic = $this->topicRepository->find($value);

            if ($topic === null) {
                throw new InvalidOptionsException("Topic with id $value was not found");
            }

            return $topic;
        });

        return $this;
    }
}
