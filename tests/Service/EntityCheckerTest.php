<?php

namespace App\Tests\Service;

use Exception;
use App\Attribut\Sortable;
use InvalidArgumentException;
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
    public function testIsFieldSortable()
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->assertTrue($checker->isFieldSortable(__Foo__::class, 'sortable'));
        $this->assertFalse($checker->isFieldSortable(__Foo__::class, 'notSortable'));
    }

    public function testIsFieldSortableWithUnknownClass()
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(Exception::class);

        $checker->isFieldSortable('UnknownClass', 'sortable');
    }

    public function testIsFieldSortableWithUnknownField()
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(InvalidArgumentException::class);

        $checker->isFieldSortable(__Foo__::class, 'unknownField');
    }

    public function testGetSortableFields()
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->assertSame(['sortable'], $checker->getSortableFields(__Foo__::class));
    }

    public function testGetSortableFieldsWithUnknownClass()
    {
        /** @var SortableEntityChecker $service */
        $checker = self::getContainer()->get(SortableEntityChecker::class);

        $this->expectException(InvalidArgumentException::class);
        $checker->getSortableFields('UnknownClass');
    }
}
