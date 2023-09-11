<?php

namespace App\Tests\OptionsResolver;

use PHPUnit\Framework\TestCase;
use App\OptionsResolver\PaginatorOptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class PaginationOptionsResolverTest extends TestCase
{
    public function testConfigurePage()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $page = 5;

        $result = $resolver->resolve(['page' => $page]);

        $this->assertSame($page, $result['page']);
    }

    public function testConfigurePageDefault()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $result = $resolver->resolve([]);

        $this->assertSame(1, $result['page']);
    }

    public function testConfigurePageInvalidType()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['page' => 'invalid']);
    }

    public function testConfigurePageInvalidValue()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configurePage();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['page' => 0]);
    }

    public function testConfigureOrder()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $order = 'DESC';

        $result = $resolver->resolve(['order' => $order]);

        $this->assertSame($order, $result['order']);
    }

    public function testConfigureOrderDefault()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $result = $resolver->resolve([]);

        $this->assertSame('ASC', $result['order']);
    }

    public function testConfigureOrderInvalidType()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['order' => 123]);
    }

    public function testConfigureOrderInvalidValue()
    {
        $resolver = new PaginatorOptionsResolver();
        $resolver->configureOrder();

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['order' => 'INVALID']);
    }

    public function testConfigureSort()
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $sort = 'name';

        $result = $resolver->resolve(['sort' => $sort]);

        $this->assertSame($sort, $result['sort']);
    }

    public function testConfigureSortDefault()
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $result = $resolver->resolve([]);

        $this->assertSame('id', $result['sort']);
    }

    public function testConfigureSortInvalidType()
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['sort' => 123]);
    }

    public function testConfigureSortInvalidValue()
    {
        $resolver = new PaginatorOptionsResolver();
        $sortableFields = ['id', 'name', 'created_at'];
        $resolver->configureSort($sortableFields);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(['sort' => 'invalid_field']);
    }
}
