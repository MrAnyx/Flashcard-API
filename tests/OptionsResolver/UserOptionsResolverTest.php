<?php

declare(strict_types=1);

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\UserOptionsResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UserOptionsResolverTest extends KernelTestCase
{
    public function testConfigureUsername(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureUsername(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('username', $result));
    }

    public function testConfigureUsernameRequired(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureUsername(true);

        $username = 'Username';

        $result = $resolver->resolve(['username' => $username]);

        $this->assertSame($username, $result['username']);
    }

    public function testConfigureUsernameRequiredWithMissingParameter(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureUsername(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureUsernameInvalidData(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureUsername(true);

        $username = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['username' => $username]);
    }

    public function testConfigureEmail(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureEmail(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('email', $result));
    }

    public function testConfigureEmailRequired(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureEmail(true);

        $email = 'Email';

        $result = $resolver->resolve(['email' => $email]);

        $this->assertSame($email, $result['email']);
    }

    public function testConfigureEmailRequiredWithMissingParameter(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureEmail(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureEmailInvalidData(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureEmail(true);

        $email = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['email' => $email]);
    }

    public function testConfigurePassword(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configurePassword(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('password', $result));
    }

    public function testConfigurePasswordRequired(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configurePassword(true);

        $password = 'Password';

        $result = $resolver->resolve(['password' => $password]);

        $this->assertSame($password, $result['password']);
    }

    public function testConfigurePasswordRequiredWithMissingParameter(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configurePassword(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigurePasswordInvalidData(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configurePassword(true);

        $password = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['password' => $password]);
    }

    public function testConfigureRoles(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureRoles(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('roles', $result));
    }

    public function testConfigureRolesRequired(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureRoles(true);

        $roles = ['Role'];

        $result = $resolver->resolve(['roles' => $roles]);

        $this->assertSame($roles, $result['roles']);
    }

    public function testConfigureRolesRequiredWithMissingParameter(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureRoles(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureRolesInvalidData(): void
    {
        /** @var UserOptionsResolver $resolver */
        $resolver = self::getContainer()->get(UserOptionsResolver::class);
        $resolver->configureRoles(true);

        $roles = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['roles' => $roles]);
    }
}
