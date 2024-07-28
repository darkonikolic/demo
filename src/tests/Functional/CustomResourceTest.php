<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

class CustomResourceTest extends ApiTestCase
{
    public function testUserCanGetData()
    {
        $number = 1;

        $json = $this->browser()
            ->post(
                '/api/custom',
                [
                    'json' => [
                        'number' => $number,
                    ]
                ]
            )
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertJsonStringEqualsJsonString(
            json_encode(
                [
                    'number' => 1,
                    'numberIncreased' => $number + 1
                ]
            ),
            $json
        );
    }
}
