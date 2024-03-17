<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Model\Paginator;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TopicRepositoryTest extends KernelTestCase
{
    public function testFindAllWithPaginationWithoutUser(): void
    {
        /** @var TopicRepository $topicRepository */
        $topicRepository = self::getContainer()->get(TopicRepository::class);

        $result = $topicRepository->findAllWithPagination(1, 'id', 'ASC');

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }

    public function testFindAllWithPaginationWithUser(): void
    {
        /** @var TopicRepository $topicRepository */
        $topicRepository = self::getContainer()->get(TopicRepository::class);

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $user = $userRepository->find(1);

        $result = $topicRepository->findAllWithPagination(1, 'id', 'ASC', $user);

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());

        foreach ($result->getData() as $data) {
            $this->assertSame($user, $data->getAuthor());
        }
    }
}
