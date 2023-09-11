<?php

namespace App\Tests\OptionsResolver;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\OptionsResolver\UnitOptionsResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UnitOptionsResolverTest extends KernelTestCase
{
    public function testConfigureName(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureName(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(array_key_exists('name', $result));
    }

    public function testConfigureNameRequired(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureName(true);

        $name = 'cool name';

        $result = $resolver->resolve(['name' => $name]);

        $this->assertSame($name, $result['name']);
    }

    public function testConfigureNameRequiredWithMissingParameter(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureName(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureNameInvalidData(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureName(true);

        $name = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['name' => $name]);
    }

    public function testConfigureTopic(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureTopic(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(array_key_exists('topic', $result));
    }

    public function testConfigureTopicRequired(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureTopic(true);

        /** @var Topic $topic */
        $topic = self::getContainer()->get(TopicRepository::class)->find(1);

        $result = $resolver->resolve(['topic' => $topic->getId()]);

        $this->assertSame($topic, $result['topic']);
    }

    public function testConfigureTopicRequiredWithMissingParameter(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureTopic(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureTopicInvalidData(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureTopic(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['topic' => 'Hello World']);
    }

    public function testConfigureTopicUnknownUser(): void
    {
        /** @var UnitOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UnitOptionsResolver::class);
        $resolver->configureTopic(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['topic' => -1]);
    }
}
