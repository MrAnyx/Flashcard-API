<?php

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginWithEmail()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => $user->getEmail(),
            'password' => 'password',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('refresh_token', json_decode($client->getResponse()->getContent(), true));
    }

    public function testLoginWithUsername()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => $user->getUsername(),
            'password' => 'password',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('refresh_token', json_decode($client->getResponse()->getContent(), true));
    }

    public function testLoginWithBadPassword()
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

    public function testLoginWithBadIdentifier()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $content = [
            'identifier' => 'invalid_indentifier',
            'password' => 'password',
        ];

        $client->request('POST', '/api/auth/login', content: json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
