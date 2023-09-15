<?php

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetMeWithAuthenticatedUser()
    {
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        $client->loginUser($user);
        $client->request('GET', '/api/users/me');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetMeWithoutAuthenticatedUser()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/me');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJson($client->getResponse()->getContent());
    }
}
