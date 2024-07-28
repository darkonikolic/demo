<?php

namespace App\Tests\Functional;

use App\Factory\ApiTokenFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testUserCanGetData()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $json = $this->browser()
            ->get(
                '/api/user',
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

    public function testUserCanGetOnlyHisOwnData()
    {
        UserFactory::createMany(5);
        $user = UserFactory::createOne();

        $json = $this->browser()
            ->actingAs($user)
            ->get(
                '/api/user',
                [
                    'headers' => ['accept' => 'application/json']
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
