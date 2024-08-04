<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\TypeConverter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class __TypeConverterTest__
{
    public int $intProp;
    public float $floatProp;
    public string $stringProp;
    public bool $boolProp;
    public array $arrayProp;
    public object $objectProp;
    public iterable $iterableProp;
    public \Closure $closureProp;
    public string|int $validUnionProp;
    public \DateTime|\DateTimeImmutable $invalidUnionProp;
    public \Traversable&\Countable $intersectionProp;
}

class TypeConverterTest extends KernelTestCase
{
    public function stringToTypeProvider()
    {
        return [
            ['123', 'intProp', 123],
            ['123.45', 'floatProp', 123.45],
            ['some string', 'stringProp', 'some string'],
            ['true', 'boolProp', true],
            ['false', 'boolProp', false],
            ['[1,2,3]', 'arrayProp', [1, 2, 3]],
            ['{"a":1,"b":2}', 'objectProp', ['a' => 1, 'b' => 2]],
            ['[1,2,3]', 'iterableProp', [1, 2, 3]],
        ];
    }

    /**
     * @dataProvider stringToTypeProvider
     */
    public function testConvertStringToType($value, $property, $expected): void
    {
        $reflection = new \ReflectionProperty(__TypeConverterTest__::class, $property);
        $type = $reflection->getType();
        $result = TypeConverter::convertStringToType($value, $type);
        $this->assertSame($expected, $result);
    }

    public function testUnsupportedType(): void
    {
        $this->expectException(\RuntimeException::class);
        $reflection = new \ReflectionProperty(__TypeConverterTest__::class, 'closureProp');
        $type = $reflection->getType();
        TypeConverter::convertStringToType('some value', $type);
    }

    public function testValidUnionType(): void
    {
        $reflection = new \ReflectionProperty(__TypeConverterTest__::class, 'validUnionProp');
        $type = $reflection->getType();
        $result = TypeConverter::convertStringToType('some value', $type);
        $this->assertSame('some value', $result);
    }

    public function testInvalidUnionType(): void
    {
        $this->expectException(\RuntimeException::class);
        $reflection = new \ReflectionProperty(__TypeConverterTest__::class, 'invalidUnionProp');
        $type = $reflection->getType();
        TypeConverter::convertStringToType('some value', $type);
    }

    public function testIntersectionType(): void
    {
        $this->expectException(\RuntimeException::class);
        $reflection = new \ReflectionProperty(__TypeConverterTest__::class, 'intersectionProp');
        $type = $reflection->getType();
        TypeConverter::convertStringToType('some value', $type);
    }
}
