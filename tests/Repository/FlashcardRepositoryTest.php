<?php

namespace App\Tests\Repository;

use App\Model\Paginator;
use App\Repository\FlashcardRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlashcardRepositoryTest extends KernelTestCase
{
    public function testFindAllWithPagination(): void
    {
        /** @var FlashcardRepository $flashcardRepository */
        $flashcardRepository = self::getContainer()->get(FlashcardRepository::class);

        $result = $flashcardRepository->findAllWithPagination(1, 'id', 'ASC');

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }
}
