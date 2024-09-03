<?php

declare(strict_types=1);

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginWithEmail(): void
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => $user->getEmail(),
            'password' => 'Password1!',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true)['data']);
    }

    public function testLoginWithUsername(): void
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => $user->getUsername(),
            'password' => 'Password1!',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true)['data']);
    }

    public function testLoginWithBadPassword(): void
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => $user->getEmail(),
            'password' => 'invalid_password',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginWithBadIdentifier(): void
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => 'invalid_indentifier',
            'password' => 'Password1!',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
