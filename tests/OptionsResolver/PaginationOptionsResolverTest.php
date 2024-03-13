<?php

declare(strict_types=1);

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\PaginatorOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class PaginationOptionsResolverTest extends TestCase
{
    public function testConfigurePage(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $page = 5;

        $result = $resolver->resolve(['page' => $page]);

        $this->assertSame($page, $result['page']);
    }

    public function testConfigurePageDefault(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $result = $resolver->resolve([]);

        $this->assertSame(1, $result['page']);
    }

    public function testConfigurePageInvalidType(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['page' => 'invalid']);
    }

    public function testConfigurePageInvalidValue(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['page' => 0]);
    }

    public function testConfigureOrder(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $order = 'DESC';

        $result = $resolver->resolve(['order' => $order]);

        $this->assertSame($order, $result['order']);
    }

    public function testConfigureOrderDefault(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $result = $resolver->resolve([]);

        $this->assertSame('ASC', $result['order']);
    }

    public function testConfigureOrderInvalidType(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['order' => 123]);
    }

    public function testConfigureOrderInvalidValue(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['order' => 'INVALID']);
    }

    public function testConfigureSort(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $sort = 'name';

        $result = $resolver->resolve(['sort' => $sort]);

        $this->assertSame($sort, $result['sort']);
    }

    public function testConfigureSortDefault(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $result = $resolver->resolve([]);

        $this->assertSame('id', $result['sort']);
    }

    public function testConfigureSortInvalidType(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['sort' => 123]);
    }

    public function testConfigureSortInvalidValue(): void
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['sort' => 'invalid_field']);
    }
}
