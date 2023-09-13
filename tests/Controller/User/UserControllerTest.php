<?php

use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetMeWithAuthenticatedUser()
    {
        // Create a test client
        $client = static::createClient();

        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        // Perform a GET request to the /api/users/me endpoint
        $client->loginUser($user);
        $client->request('GET', '/api/users/me');

        // Check if the response is successful (HTTP status code 200)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Check if the response content contains user data (customize this based on your user serialization logic)
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetMeWithoutAuthenticatedUser()
    {
        // Create a test client
        $client = static::createClient();

        // Perform a GET request to the /api/users/me endpoint without authenticating
        $client->request('GET', '/api/users/me');

        // Check if the response is unauthorized (HTTP status code 401)
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Check if the response content contains an error message (customize this based on your ApiException logic)
        $this->assertJson($client->getResponse()->getContent());
    }
}
