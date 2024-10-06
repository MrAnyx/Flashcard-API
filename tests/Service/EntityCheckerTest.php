<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Attribute\Sortable;
use App\Service\SortableEntityChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class __Foo__
{
    #[Sortable]
    private string $sortable;

    private string $notSortable;
}

class SortableEntityCheckerTest extends KernelTestCase
{
    public function testIsFieldSortable(): void
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->assertTrue($checker->isFieldSortable(__Foo__::class, 'sortable'));
        $this->assertFalse($checker->isFieldSortable(__Foo__::class, 'notSortable'));
    }

    public function testIsFieldSortableWithUnknownClass(): void
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(\Exception::class);

        $checker->isFieldSortable('UnknownClass', 'sortable');
    }

    public function testIsFieldSortableWithUnknownField(): void
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(\InvalidArgumentException::class);

        $checker->isFieldSortable(__Foo__::class, 'unknownField');
    }

    public function testGetSortableFields(): void
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->assertSame(['sortable'], $checker->getSortableFields(__Foo__::class));
    }

    public function testGetSortableFieldsWithUnknownClass(): void
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(\InvalidArgumentException::class);
        $checker->getSortableFields('UnknownClass');
    }
}
