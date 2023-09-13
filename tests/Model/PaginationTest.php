<?php

namespace App\Tests\Model;

use App\Model\Paginator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaginatorTest extends KernelTestCase
{
    private Paginator $paginator;

    public function setUp(): void
    {
        // Create the Query object
        $em = self::getContainer()->get('doctrine')->getManager();
        $query = $em->createQueryBuilder()
            ->select('f')
            ->from('App\Entity\Flashcard', 'f')
            ->getQuery();

        // Create the Paginator object
        $this->paginator = new Paginator($query, 2);
    }

    public function testTotal(): void
    {
        $this->assertIsInt($this->paginator->getTotal());
    }

    public function testData(): void
    {
        $this->assertIsArray($this->paginator->getData());
    }

    public function testCount(): void
    {
        $this->assertIsInt($this->paginator->getCount());
    }

    public function testTotalPages(): void
    {
        $this->assertIsInt($this->paginator->getTotalPages());
    }

    public function testCurrentPage(): void
    {
        $this->assertIsInt($this->paginator->getCurrentPage());
    }

    public function testOffset(): void
    {
        $this->assertIsInt($this->paginator->getOffset());
    }

    public function testItemsPerPage(): void
    {
        $this->assertIsInt($this->paginator->getItemsPerPage());
    }

    public function testHasNextPage(): void
    {
        $this->assertIsBool($this->paginator->hasNextPage());
        $this->assertTrue($this->paginator->hasNextPage());
    }

    public function testHasNextPageWithoutNextPage(): void
    {
        // Create the Query object
        $em = self::getContainer()->get('doctrine')->getManager();
        $query = $em->createQueryBuilder()
            ->select('f')
            ->from('App\Entity\Flashcard', 'f')
            ->where('f.id = -1')
            ->getQuery();

        // Create the Paginator object
        $paginator = new Paginator($query);

        $this->assertIsBool($paginator->hasNextPage());
        $this->assertFalse($paginator->hasNextPage());
    }

    public function testHasPreviousPage(): void
    {
        $this->assertIsBool($this->paginator->hasPreviousPage());
        $this->assertTrue($this->paginator->hasPreviousPage());
    }

    public function testHasPreviousPageWithoutNextPage(): void
    {
        // Create the Query object
        $em = self::getContainer()->get('doctrine')->getManager();
        $query = $em->createQueryBuilder()
            ->select('f')
            ->from('App\Entity\Flashcard', 'f')
            ->where('f.id = -1')
            ->getQuery();

        // Create the Paginator object
        $paginator = new Paginator($query);

        $this->assertIsBool($paginator->hasPreviousPage());
        $this->assertFalse($paginator->hasPreviousPage());
    }

    public function testIterator(): void
    {
        $arrayPaginator = $this->paginator->getIterator();
        $this->assertArrayHasKey('data', $arrayPaginator);
        $this->assertArrayHasKey('pagination', $arrayPaginator);

        $this->assertArrayHasKey('total', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('count', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('offset', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('items_per_page', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('total_pages', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('current_page', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('has_next_page', $arrayPaginator['pagination']);
        $this->assertArrayHasKey('has_previous_page', $arrayPaginator['pagination']);
    }
}
