<?php

namespace App\Tests\Entity;

use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Topic;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TopicTest extends KernelTestCase
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
        $topic = new Topic();

        $this->assertNull($topic->getId());
        $this->assertNull($topic->getName());
        $this->assertNull($topic->getCreatedAt());
        $this->assertNull($topic->getUpdatedAt());
        $this->assertNull($topic->getAuthor());
        $this->assertEmpty($topic->getUnits());
    }

    public function testId()
    {
        /** @var Topic $topic */
        $topic = self::getContainer()->get(TopicRepository::class)->find(1);
        $this->assertIsInt($topic->getId());
    }

    public function testName()
    {
        $topic = new Topic();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($topic, 'name');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $topic->setName('Very long name for a topic with more than 35 chars');
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($topic, 'name');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $title = 'Test topic';
        $topic->setName($title);
        $this->assertSame($title, $topic->getName());
    }

    public function testCreatedAt()
    {
        $topic = new Topic();
        $this->em->persist($topic);
        $this->assertInstanceOf(DateTimeImmutable::class, $topic->getCreatedAt());
        $this->em->detach($topic);
    }

    public function testUpdatedAt()
    {
        $topic = new Topic();
        $this->em->persist($topic);
        $this->assertInstanceOf(DateTimeImmutable::class, $topic->getUpdatedAt());
        $this->em->detach($topic);
    }

    public function testAuthor()
    {
        $topic = new Topic();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($topic, 'author');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $topic->setAuthor($user);
        $this->assertSame($user, $topic->getAuthor());
    }

    public function testUnits()
    {
        $topic = new Topic();

        /** @var Unit $unit */
        $unit = self::getContainer()->get(UnitRepository::class)->find(1);

        $this->assertEmpty($topic->getUnits());

        $topic->addUnit($unit);
        $this->assertContains($unit, $topic->getUnits());
        $this->assertSame($topic, $unit->getTopic());

        $topic->removeUnit($unit);
        $this->assertNotContains($unit, $topic->getUnits());
        $this->assertSame(null, $unit->getTopic());
    }
}
