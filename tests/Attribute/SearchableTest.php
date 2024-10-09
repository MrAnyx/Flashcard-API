<?php

declare(strict_types=1);

namespace App\Tests\Attribute;

use App\Attribute\Searchable;
use PHPUnit\Framework\TestCase;

class SearchableTest extends TestCase
{
    public function testAttributeWithDefaultValues(): void
    {
        // Instantiate the Searchable attribute with default parameters
        $attribute = new Searchable();

        $this->assertNull($attribute->converterFqcn);
        $this->assertSame([], $attribute->converterConstructorParams);
    }

    public function testAttributeWithCustomValues(): void
    {
        // Instantiate the Searchable attribute with custom parameters
        $converterFqcn = 'App\\Serializer\\CustomSerializer';
        $params = ['param1' => 'value1', 'param2' => 'value2'];

        $attribute = new Searchable($converterFqcn, $params);

        $this->assertSame($converterFqcn, $attribute->converterFqcn);
        $this->assertSame($params, $attribute->converterConstructorParams);
    }

    public function testEmptyConstructorParams(): void
    {
        // Instantiate the Searchable attribute with a converterFqcn but empty constructor params
        $converterFqcn = 'App\\Serializer\\AnotherSerializer';

        $attribute = new Searchable($converterFqcn);

        $this->assertSame($converterFqcn, $attribute->converterFqcn);
        $this->assertSame([], $attribute->converterConstructorParams);
    }
}
