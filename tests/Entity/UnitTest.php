<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Flashcard;
use App\Entity\Topic;
use App\Entity\Unit;
use App\Repository\FlashcardRepository;
use App\Repository\TopicRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitTest extends KernelTestCase
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
        $unit = new Unit();

        $this->assertNull($unit->getId());
        $this->assertNull($unit->getName());
        $this->assertNull($unit->getCreatedAt());
        $this->assertNull($unit->getUpdatedAt());
        $this->assertNull($unit->getTopic());
        $this->assertEmpty($unit->getFlashcards());
    }

    public function testId(): void
    {
        /** @var Unit $unit */
        $unit = self::getContainer()->get(UnitRepository::class)->find(1);
        $this->assertIsInt($unit->getId());
    }

    public function testName(): void
    {
        $unit = new Unit();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($unit, 'name');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $unit->setName('Very long name for a unit with more than 35 chars');
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($unit, 'name');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $title = 'Test unit';
        $unit->setName($title);
        $this->assertSame($title, $unit->getName());
    }

    public function testCreatedAt(): void
    {
        $unit = new Unit();
        $this->em->persist($unit);
        $this->assertInstanceOf(\DateTimeImmutable::class, $unit->getCreatedAt());
        $this->em->detach($unit);
    }

    public function testUpdatedAt(): void
    {
        $unit = new Unit();
        $this->em->persist($unit);
        $this->assertInstanceOf(\DateTimeImmutable::class, $unit->getUpdatedAt());
        $this->em->detach($unit);
    }

    public function testTopic(): void
    {
        $unit = new Unit();

        /** @var Topic $topic */
        $topic = self::getContainer()->get(TopicRepository::class)->find(1);

        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($unit, 'topic');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $unit->setTopic($topic);
        $this->assertSame($topic, $unit->getTopic());
    }

    public function testFlashcards(): void
    {
        $unit = new Unit();

        /** @var Flashcard $flashcard */
        $flashcard = self::getContainer()->get(FlashcardRepository::class)->find(1);

        $this->assertEmpty($unit->getFlashcards());

        $unit->addFlashcard($flashcard);
        $this->assertContains($flashcard, $unit->getFlashcards());
        $this->assertSame($unit, $flashcard->getUnit());

        $unit->removeFlashcard($flashcard);
        $this->assertNotContains($flashcard, $unit->getFlashcards());
        $this->assertNull($flashcard->getUnit());
    }
}
