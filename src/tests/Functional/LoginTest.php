<?php

namespace App\Tests\Functional;

use App\Factory\ApiTokenFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginTest extends ApiTestCase
{
    use ResetDatabase;

    public function testLoginWithNotValidCredentials()
    {
        $this->browser()
            ->post(
                '/auth',
                [
                    'json' => ['email' => '', 'password' => ''],
                    'headers' => ['accept' => 'application/json']
                ]
            )
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testLoginWithValidCredentials()
    {
        $user = UserFactory::createOne();
        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user
        ]);

        $json = $this->browser()
            ->post(
                '/auth',
                [
                    'json' => ['email' => $user->getEmail(), 'password' => 'password'],
                    'headers' => ['accept' => 'application/json']
                ]
            )
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode(['token' => $apiToken->getToken()]),
            $json
        );
    }

    public function testAuthenticationWithInvalidToken()
    {
        $user = UserFactory::createOne();

        ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $this->browser()
            ->get(
                '/api/user',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer sadf'
                    ]
                ]
            )
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ;
    }

    public function testAuthenticationWithValidToken()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $json = $this->browser()
            ->get(
                '/api/user/',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json()
        ;

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                    'username' => $user->getUsername(),
                ]
            ]),
            $json
        );
    }
}
