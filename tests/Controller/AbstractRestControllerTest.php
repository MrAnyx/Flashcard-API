<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Attribut\Sortable;
use App\Controller\AbstractRestController;
use App\Exception\ApiException;
use App\Model\Page;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;

class __Foo__
{
    #[Sortable]
    private int $id;

    #[Length(max: 5)]
    public string $username;
}

class AbstractRestControllerTest extends KernelTestCase
{
    public function testGetPaginationParameter(): void
    {
        /** @var AbstractRestController $abstractController */
        $abstractController = self::getContainer()->get(AbstractRestController::class);
        $request = new Request(['page' => '1', 'sort' => 'id', 'order' => 'ASC']);

        $result = $abstractController->getPaginationParameter(__Foo__::class, $request);

        $this->assertInstanceOf(Page::class, $result);

        $this->assertIsInt($result->page);
        $this->assertSame(1, $result->page);

        $this->assertIsString($result->sort);
        $this->assertSame('id', $result->sort);

        $this->assertIsString($result->order);
        $this->assertSame('ASC', $result->order);
    }

    public function testGetPaginationParameterInvalidPage(): void
    {
        /** @var AbstractRestController $abstractController */
        $abstractController = self::getContainer()->get(AbstractRestController::class);
        $request = new Request(['page' => '-1', 'sort' => 'id', 'order' => 'ASC']);

        $this->expectException(ApiException::class);
        $result = $abstractController->getPaginationParameter(__Foo__::class, $request);
    }

    public function testValidateEntity(): void
    {
        /** @var AbstractRestController $abstractController */
        $abstractController = self::getContainer()->get(AbstractRestController::class);

        $entity = new __Foo__();
        $entity->username = 'VeryLongUsernameForTheValidation';

        $this->expectException(ApiException::class);
        $abstractController->validateEntity($entity);
    }

    public function testValidateEntityWithValidationGroup(): void
    {
        /** @var AbstractRestController $abstractController */
        $abstractController = self::getContainer()->get(AbstractRestController::class);

        $entity = new __Foo__();
        $entity->username = 'VeryLongUsernameForTheValidation';

        try {
            $abstractController->validateEntity($entity, ['special_group']);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
