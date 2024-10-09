<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Model\Page;
use App\Model\Paginator;
use App\Repository\FlashcardRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlashcardRepositoryTest extends KernelTestCase
{
    public function testpaginateAndFilterAll(): void
    {
        /** @var FlashcardRepository $flashcardRepository */
        $flashcardRepository = self::getContainer()->get(FlashcardRepository::class);

        $result = $flashcardRepository->paginateAndFilterAll(new Page(1, 'id', 'ASC', 25));

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }
}
