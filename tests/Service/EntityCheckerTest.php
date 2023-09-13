<?php

namespace App\Tests\Service;

use Exception;
use App\Attribut\Sortable;
use InvalidArgumentException;
use App\Service\EntityChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class __Foo__
{
    #[Sortable]
    private string $sortable;

    private string $notSortable;
}

class EntityCheckerTest extends KernelTestCase
{
    public function testEntityExists()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertTrue($checker->entityExists(__Foo__::class));
        $this->assertFalse($checker->entityExists('UnknownClass'));
    }

    public function testFieldExists()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertTrue($checker->fieldExists(__Foo__::class, 'sortable'));
        $this->assertFalse($checker->fieldExists(__Foo__::class, 'unknownField'));
    }

    public function testIsFieldSortable()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertTrue($checker->isFieldSortable(__Foo__::class, 'sortable'));
        $this->assertFalse($checker->isFieldSortable(__Foo__::class, 'notSortable'));
    }

    public function testIsFieldSortableWithUnknownClass()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->expectException(Exception::class);

        $checker->isFieldSortable('UnknownClass', 'sortable');
    }

    public function testIsFieldSortableWithUnknownField()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->expectException(InvalidArgumentException::class);

        $checker->isFieldSortable(__Foo__::class, 'unknownField');
    }

    public function testGetSortableFields()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertSame(['sortable'], $checker->getSortableFields(__Foo__::class));
    }

    public function testGetSortableFieldsWithUnknownClass()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->expectException(InvalidArgumentException::class);
        $checker->getSortableFields('UnknownClass');
    }
}
