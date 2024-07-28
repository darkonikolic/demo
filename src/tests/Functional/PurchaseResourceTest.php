<?php

namespace App\Tests\Functional;

use App\Factory\ApiTokenFactory;
use App\Factory\ProductFactory;
use App\Factory\PurchaseFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class PurchaseResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testUserCanGetData()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $product = ProductFactory::createOne([
            'sku' => 'sku 1',
            'name' => 'Product ABC'
        ]);

        PurchaseFactory::createOne([
            'product' => $product,
            'user' => $user
        ]);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'id' => 1,
                    'sku' => $product->getSku(),
                    'name' => $user->getUsername()
                ]
            ]),
            $json
        );
    }

    public function testUserCanGetOnlyHisOwnData()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $product = ProductFactory::createOne([
            'sku' => 'sku 1',
            'name' => 'Product ABC'
        ]);

        PurchaseFactory::createOne([
            'product' => $product,
            'user' => $user
        ]);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'id' => 1,
                    'sku' => $product->getSku(),
                    'name' => $user->getUsername()
                ]
            ]),
            $json
        );


        $userWithoutPurchases = UserFactory::createOne();

        $apiTokenForUserWithoutPurchases = ApiTokenFactory::createOne([
            'ownedBy' => $userWithoutPurchases,
            'scopes' => ['ROLE_USER']
        ]);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiTokenForUserWithoutPurchases->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(json_encode([]), $json);
    }

    public function testUserCanAddSeveralPurchases()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $product1 = ProductFactory::createOne([
            'sku' => 'sku 1',
            'name' => 'Product ABC'
        ]);

        $product2 = ProductFactory::createOne([
            'sku' => 'sku 2',
            'name' => 'Product ABC'
        ]);

        $this->browser()
            ->post(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ],
                    'json' => ['sku' => 'sku 1']
                ]
            )
            ->assertStatus(Response::HTTP_CREATED)
            ->post(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ],
                    'json' => ['sku' => 'sku 2']
                ]
            )
            ->assertStatus(Response::HTTP_CREATED);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'id' => 1,
                    'sku' => $product1->getSku(),
                    'name' => $user->getUsername()
                ],
                [
                    'id' => 2,
                    'sku' => $product2->getSku(),
                    'name' => $user->getUsername()
                ]
            ]),
            $json
        );
    }

    public function testUserCantAddPurchaseWithInvalidInput()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $this->browser()
            ->post(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ],
                    'json' => []
                ]
            )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUserCanDeleteHisPurchases()
    {
        $user = UserFactory::createOne();

        $apiToken = ApiTokenFactory::createOne([
            'ownedBy' => $user,
            'scopes' => ['ROLE_USER']
        ]);

        $product = ProductFactory::createOne([
            'sku' => 'sku 1',
            'name' => 'Product ABC'
        ]);

        PurchaseFactory::createOne([
            'product' => $product,
            'user' => $user
        ]);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'id' => 1,
                    'sku' => $product->getSku(),
                    'name' => $user->getUsername()
                ]
            ]),
            $json
        );

        $this->browser()
            ->delete(
                '/api/user/products/' . $product->getId(),
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $json = $this->browser()
            ->get(
                '/api/user/products',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiToken->getToken()
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(json_encode([]), $json);
    }
}
