<?php

namespace App\Tests\Service;

use Exception;
use App\Attribut\Sortable;
use InvalidArgumentException;
use App\Service\EntityChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Foo
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

        $this->assertTrue($checker->entityExists(Foo::class));
        $this->assertFalse($checker->entityExists('UnknownClass'));
    }

    public function testFieldExists()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertTrue($checker->fieldExists(Foo::class, 'sortable'));
        $this->assertFalse($checker->fieldExists(Foo::class, 'unknownField'));
    }

    public function testIsFieldSortable()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertTrue($checker->isFieldSortable(Foo::class, 'sortable'));
        $this->assertFalse($checker->isFieldSortable(Foo::class, 'notSortable'));
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

        $checker->isFieldSortable(Foo::class, 'unknownField');
    }

    public function testGetSortableFields()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->assertSame(['sortable'], $checker->getSortableFields(Foo::class));
    }

    public function testGetSortableFieldsWithUnknownClass()
    {
        /** @var EntityChecker $service */
        $checker = self::getContainer()->get(EntityChecker::class);

        $this->expectException(InvalidArgumentException::class);
        $checker->getSortableFields('UnknownClass');
    }
}
