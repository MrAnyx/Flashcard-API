<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Model\Page;
use App\Model\Paginator;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnitRepositoryTest extends KernelTestCase
{
    public function testpaginateAndFilterAll(): void
    {
        /** @var UnitRepository $unitRepository */
        $unitRepository = self::getContainer()->get(UnitRepository::class);

        $result = $unitRepository->paginateAndFilterAll(new Page(1, 'id', 'ASC', 25));

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertSame(1, $result->getCurrentPage());
    }
}
