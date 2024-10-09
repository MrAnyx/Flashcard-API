<?php

declare(strict_types=1);

namespace App\Tests\Attribute;

use App\Attribute\Sortable;
use PHPUnit\Framework\TestCase;

class SortableTest extends TestCase
{
    public function testSortableAttributeInitialization(): void
    {
        // Instantiate the Sortable attribute
        $attribute = new Sortable();

        // Test that the instance is of the correct class
        $this->assertInstanceOf(Sortable::class, $attribute);
    }
}
