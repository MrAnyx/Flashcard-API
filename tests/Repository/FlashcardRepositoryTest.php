<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Model\Filter;
use App\Model\Page;
use App\Model\Paginator;
use App\Repository\FlashcardRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlashcardRepositoryTest extends KernelTestCase
{
    public function testFindAllWithPagination(): void
    {
        /** @var FlashcardRepository $flashcardRepository */
        $flashcardRepository = self::getContainer()->get(FlashcardRepository::class);

        $result = $flashcardRepository->findAllWithPagination(new Page(1, 'id', 'ASC', 25), new Filter(null, null));

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }
}
