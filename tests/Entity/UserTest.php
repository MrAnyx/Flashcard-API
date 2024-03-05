<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Topic;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private EntityManager $em;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->validator = self::getContainer()->get('validator');
    }

    private function assertArrayContainsInstanceOf(string $constraint, ConstraintViolationList $constraintViolations)
    {
        foreach ($constraintViolations as $violation) {
            if ($violation->getConstraint() instanceof $constraint) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->fail("Array does not contain an instance of $constraint");
    }

    public function testDefaultValues(): void
    {
        $user = new User();
        $user->setPassword('password');

        $this->assertNull($user->getId());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getUsername());
        $this->assertEmpty($user->getUserIdentifier());
        $this->assertNull($user->getCreatedAt());
        $this->assertNull($user->getUpdatedAt());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $this->assertSame('password', $user->getPassword());
        $this->assertNull($user->getRawPassword());
        $this->assertEmpty($user->getTopics());
    }

    public function testId()
    {
        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);
        $this->assertIsInt($user->getId());
    }

    public function testEmail()
    {
        $user = new User();

        // Test entity constraints
        $errors = $this->validator->validateProperty($user, 'email');
        $this->assertArrayContainsInstanceOf(NotBlank::class, $errors);

        $user->setEmail('veryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryverylongemail@mail.com');
        $errors = $this->validator->validateProperty($user, 'email');
        $this->assertArrayContainsInstanceOf(Length::class, $errors);

        $user->setEmail('invalid email');
        $errors = $this->validator->validateProperty($user, 'email');
        $this->assertArrayContainsInstanceOf(Email::class, $errors);

        $email = 'mail@mail.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
    }

    public function testUsername()
    {
        $user = new User();

        // Test entity constraints
        $errors = $this->validator->validateProperty($user, 'username');
        $this->assertArrayContainsInstanceOf(NotBlank::class, $errors);

        $user->setUsername('veryveryveryveryveryverylongusername');
        $errors = $this->validator->validateProperty($user, 'username');
        $this->assertArrayContainsInstanceOf(Length::class, $errors);

        $user->setUsername('invalid email');
        $errors = $this->validator->validateProperty($user, 'username');
        $this->assertArrayContainsInstanceOf(Regex::class, $errors);

        $username = 'username.Test-hello_';
        $user->setUsername($username);
        $this->assertSame($username, $user->getUsername());
    }

    public function testToken()
    {
        $user = new User();

        // Test entity constraints
        $errors = $this->validator->validateProperty($user, 'token');
        $this->assertArrayContainsInstanceOf(NotBlank::class, $errors);

        $user->setToken('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $errors = $this->validator->validateProperty($user, 'token');
        $this->assertArrayContainsInstanceOf(Length::class, $errors);

        $username = 'valid_token';
        $user->setToken($username);
        $this->assertSame($username, $user->getToken());
    }

    public function testUserIdentifier()
    {
        $user = new User();

        $username = 'username';
        $user->setUsername($username);

        $this->assertSame($username, $user->getUserIdentifier());
    }

    public function testCreatedAt()
    {
        $user = new User();
        $this->em->persist($user);
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->em->detach($user);
    }

    public function testUpdatedAt()
    {
        $user = new User();
        $this->em->persist($user);
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
        $this->em->detach($user);
    }

    public function testRoles()
    {
        $user = new User();

        $this->assertSame(['ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_TEST']);
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertContains('ROLE_TEST', $user->getRoles());

        $user->addRole('ROLE_TEST_2');
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertContains('ROLE_TEST', $user->getRoles());
        $this->assertContains('ROLE_TEST_2', $user->getRoles());
    }

    public function testEraseCredentials()
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testRawPassword()
    {
        $user = new User();
        $user->setUsername('username');
        $user->setEmail('mail@mail.com');

        // Test entity constraints
        $errors = $this->validator->validateProperty($user, 'rawPassword', ['edit:user:password']);
        $this->assertArrayContainsInstanceOf(NotBlank::class, $errors);

        // We don't test the not compromised password constraint because it is not supported

        $user->setRawPassword('password');
        $errors = $this->validator->validateProperty($user, 'rawPassword', ['edit:user:password']);
        $this->assertArrayContainsInstanceOf(PasswordStrength::class, $errors);

        $user->setRawPassword('username');
        $errors = $this->validator->validateProperty($user, 'rawPassword', ['edit:user:password']);
        $this->assertArrayContainsInstanceOf(NotEqualTo::class, $errors);

        $user->setRawPassword('mail@mail.com');
        $errors = $this->validator->validateProperty($user, 'rawPassword', ['edit:user:password']);
        $this->assertArrayContainsInstanceOf(NotEqualTo::class, $errors);

        $password = 'Sup3rStringP4$$word!';
        $user->setRawPassword($password);
        $this->assertSame($password, $user->getRawPassword());
    }

    public function testTopic()
    {
        $user = new User();

        /** @var Topic $topic */
        $topic = self::getContainer()->get(TopicRepository::class)->find(1);

        $this->assertEmpty($user->getToken());

        $user->addTopic($topic);
        $this->assertContains($topic, $user->getTopics());
        $this->assertSame($user, $topic->getAuthor());

        $user->removeTopic($topic);
        $this->assertNotContains($topic, $user->getTopics());
        $this->assertSame(null, $topic->getAuthor());
    }
}
