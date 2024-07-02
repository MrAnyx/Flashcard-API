<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Attribut\Sortable;
use App\Service\AttributeHelper;
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
        /** @var AttributeHelper $checker */
        $checker = self::getContainer()->get(AttributeHelper::class);

        $this->assertTrue($checker->hasAttribute(__Foo__::class, 'sortable', Sortable::class));
        $this->assertFalse($checker->hasAttribute(__Foo__::class, 'notSortable', Sortable::class));
    }

    public function testIsFieldSortableWithUnknownClass(): void
    {
        /** @var AttributeHelper $checker */
        $checker = self::getContainer()->get(AttributeHelper::class);

        $this->expectException(\Exception::class);

        $checker->hasAttribute('UnknownClass', 'sortable', Sortable::class);
    }

    public function testIsFieldSortableWithUnknownField(): void
    {
        /** @var AttributeHelper $checker */
        $checker = self::getContainer()->get(AttributeHelper::class);

        $this->expectException(\InvalidArgumentException::class);

        $checker->hasAttribute(__Foo__::class, 'unknownField', Sortable::class);
    }

    public function testGetSortableFields(): void
    {
        /** @var AttributeHelper $checker */
        $checker = self::getContainer()->get(AttributeHelper::class);

        $this->assertSame(['sortable'], $checker->getFieldsWithAttribute(__Foo__::class, Sortable::class));
    }

    public function testGetSortableFieldsWithUnknownClass(): void
    {
        /** @var AttributeHelper $checker */
        $checker = self::getContainer()->get(AttributeHelper::class);

        $this->expectException(\InvalidArgumentException::class);
        $checker->getFieldsWithAttribute('UnknownClass', Sortable::class);
    }
}
