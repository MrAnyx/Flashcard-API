<?php

namespace App\Tests\OptionsResolver;

use App\Entity\User;
use App\Repository\UserRepository;
use App\OptionsResolver\TopicOptionsResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class TopicOptionsResolverTest extends KernelTestCase
{
    public function testConfigureName(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureName(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(array_key_exists('name', $result));
    }

    public function testConfigureNameRequired(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureName(true);

        $name = 'cool name';

        $result = $resolver->resolve(['name' => $name]);

        $this->assertSame($name, $result['name']);
    }

    public function testConfigureNameRequiredWithMissingParameter(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureName(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureNameInvalidData(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureName(true);

        $name = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['name' => $name]);
    }

    public function testConfigureAuthor(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureAuthor(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(array_key_exists('author', $result));
    }

    public function testConfigureAuthorRequired(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureAuthor(true);

        /** @var User $author */
        $author = self::getContainer()->get(UserRepository::class)->find(1);

        $result = $resolver->resolve(['author' => $author->getId()]);

        $this->assertSame($author, $result['author']);
    }

    public function testConfigureAuthorRequiredWithMissingParameter(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureAuthor(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureAuthorInvalidData(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureAuthor(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['author' => 'Hello World']);
    }

    public function testConfigureAuthorUnknownUser(): void
    {
        /** @var TopicOptionsResolver $resolver */
        $resolver = self::getContainer()->get(TopicOptionsResolver::class);
        $resolver->configureAuthor(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['author' => -1]);
    }
}
