<?php

namespace App\Tests\Entity;

use App\Entity\Topic;
use App\Entity\Flashcard;
use Doctrine\ORM\EntityManager;
use App\Repository\UnitRepository;
use App\Repository\FlashcardRepository;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FlashcardTest extends KernelTestCase
{
    private EntityManager $em;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->validator = self::getContainer()->get('validator');
    }

    public function testDefaultValues(): void
    {
        $flashcard = new Flashcard();

        $this->assertNull($flashcard->getId());
        $this->assertNull($flashcard->getCreatedAt());
        $this->assertNull($flashcard->getUpdatedAt());
        $this->assertNull($flashcard->getFront());
        $this->assertNull($flashcard->getBack());
        $this->assertNull($flashcard->getDetails());
        $this->assertNull($flashcard->getUnit());
    }

    public function testId()
    {
        /** @var Flashcard $flashcard */
        $flashcard = self::getContainer()->get(FlashcardRepository::class)->find(1);
        $this->assertIsInt($flashcard->getId());
    }

    public function testCreatedAt()
    {
        $flashcard = new Flashcard();
        $this->em->persist($flashcard);
        $this->assertInstanceOf(\DateTimeImmutable::class, $flashcard->getCreatedAt());
        $this->em->detach($flashcard);
    }

    public function testUpdatedAt()
    {
        $flashcard = new Flashcard();
        $this->em->persist($flashcard);
        $this->assertInstanceOf(\DateTimeImmutable::class, $flashcard->getUpdatedAt());
        $this->em->detach($flashcard);
    }

    public function testFront()
    {
        $flashcard = new Flashcard();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'front');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $flashcard->setFront('Very long front for a flashcard with more than 255 chars Dolor proident nisi nostrud Lorem esse. Id dolor sunt occaecat ullamco nulla quis do. Deserunt quis ipsum et ex cillum. Sint aliquip deserunt enim nisi eiusmod eiusmod exercitation velit nisi esse veniam. Hello World!');
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'front');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $front = 'Test front';
        $flashcard->setFront($front);
        $this->assertSame($front, $flashcard->getFront());
    }

    public function testBack()
    {
        $flashcard = new Flashcard();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'back');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $flashcard->setBack('Very long back for a flashcard with more than 255 chars Dolor proident nisi nostrud Lorem esse. Id dolor sunt occaecat ullamco nulla quis do. Deserunt quis ipsum et ex cillum. Sint aliquip deserunt enim nisi eiusmod eiusmod exercitation velit nisi esse veniam. Hello World!');
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'back');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $back = 'Test back';
        $flashcard->setBack($back);
        $this->assertSame($back, $flashcard->getBack());
    }

    public function testDetails()
    {
        $flashcard = new Flashcard();

        $flashcard->setDetails('Very long details for a flashcard with more than 1000 chars Dolor proident nisi nostrud Lorem esse. Esse occaecat duis sunt exercitation. Sunt laborum dolore proident consectetur mollit nostrud non reprehenderit. Lorem anim ad magna sit sit mollit cillum aute ut. Nostrud aliquip mollit sint id occaecat eiusmod excepteur. Id dolor sunt occaecat ullamco nulla quis do. Deserunt quis ipsum et ex cillum. Sint aliquip deserunt enim nisi eiusmod eiusmod exercitation velit nisi esse veniam. Pariatur sint quis culpa labore sunt labore eu mollit ea mollit eiusmod non enim. Nostrud eiusmod minim laboris voluptate dolore nostrud aute et exercitation elit. Anim aute quis ea dolore fugiat amet. Tempor quis do enim consectetur magna amet est anim. Consectetur irure ut quis labore et mollit dolore ipsum sit incididunt incididunt cupidatat proident. Qui occaecat minim proident ex eu eiusmod elit. Veniam eu ullamco ex proident cupidatat. Ipsum in Lorem non et officia dolor quis esse culpa ex culpa. Ipsum laborum elit id culpa esse. Amet minim fugiat quis ad enim');
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'details');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $details = 'Test details';
        $flashcard->setDetails($details);
        $this->assertSame($details, $flashcard->getDetails());
    }

    public function testUnit()
    {
        $flashcard = new Flashcard();

        /** @var Topic $topic */
        $unit = self::getContainer()->get(UnitRepository::class)->find(1);

        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($flashcard, 'unit');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $flashcard->setUnit($unit);
        $this->assertSame($unit, $flashcard->getUnit());
    }
}
