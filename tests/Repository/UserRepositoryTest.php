<?php

namespace App\Tests\Repository;

use App\Model\Paginator;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class __Animal__ implements PasswordAuthenticatedUserInterface
{
    public function getPassword(): ?string
    {
        return '';
    }
}

class UserRepositoryTest extends KernelTestCase
{
    public function testFindAllWithPagination(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $result = $userRepository->findAllWithPagination(1, 'id', 'ASC');

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }

    public function testLoadUserByIdentifier(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $user = $userRepository->find(1);

        $resultByUsername = $userRepository->loadUserByIdentifier($user->getUsername());
        $resultByEmail = $userRepository->loadUserByIdentifier($user->getEmail());

        $this->assertSame($user, $resultByUsername);
        $this->assertSame($user, $resultByEmail);
    }

    public function testLoadUserByNullIdentifier(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $resultByUsername = $userRepository->loadUserByIdentifier('Hello World!');

        $this->assertSame(null, $resultByUsername);
    }

    public function testUpgradePassword(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['username' => 'test']);

        $newPassword = uniqid();
        $userRepository->upgradePassword($user, $newPassword);

        $this->assertSame($newPassword, $user->getPassword());
    }

    public function testUpgradePasswordUnsupportedUser(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $entity = new __Animal__();

        $this->expectException(UnsupportedUserException::class);
        $userRepository->upgradePassword($entity, 'new password');
    }
}
