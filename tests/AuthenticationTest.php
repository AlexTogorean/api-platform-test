<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AuthenticationTest extends ApiTestCase
{
    public function testLogin(): void
    {
        $response = static::createClient()->request('POST', 'http://api-test-php/api/login_check', [
//            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'user1',
                'password' => 'pass1',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        static::createClient()->request('GET', '/api/products');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        static::createClient()->request('GET', '/api/products', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}
