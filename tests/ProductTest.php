<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class ProductTest extends ApiTestCase
{
    private ?string $token = null;

    public function testGetProducts(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
            'hydra:member' => [
                [
                    '@id' => '/api/products/1',
                    '@type' => 'Product',
                    'name' => 'Product 1',
                    'price' => 100,
                ],
                [
                    '@id' => '/api/products/2',
                    '@type' => 'Product',
                    'name' => 'Product 2',
                    'price' => 200,
                ],
                [
                    '@id' => '/api/products/3',
                    '@type' => 'Product',
                    'name' => 'Product 3',
                    'price' => 300,
                ],
                [
                    '@id' => '/api/products/4',
                    '@type' => 'Product',
                    'name' => 'Product 4',
                    'price' => 400,
                ],
            ]
        ]);
    }

    public function testGetProduct(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products/1');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products/1',
            '@type' => 'Product',
            'name' => 'Product 1',
            'price' => 100,
        ]);
    }

    public function testGetProductsFromOrder(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders/1/products');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/orders/1/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
            'hydra:member' => [
                [
                    '@id' => '/api/products/1',
                    '@type' => 'Product',
                    'name' => 'Product 1',
                    'price' => 100,
                ],
                [
                    '@id' => '/api/products/3',
                    '@type' => 'Product',
                    'name' => 'Product 3',
                    'price' => 300,
                ],
            ]
        ]);
    }

    public function testCreateProduct(): void
    {
        // Test new product creation
        $response = $this->createClientWithCredentials()->request('POST', 'http://api-test-php/api/products', [
            'json' => [
                'name' => 'Product new',
                'price' => 1000,
            ]
        ]);

        $this->assertResponseIsSuccessful();

        // Test if the new product exists in the list of products
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products/5');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products/5',
            '@type' => 'Product',
            'name' => 'Product new',
            'price' => 1000,
        ]);
    }

    public function testUpdateProduct(): void
    {
        // Test product update
        $response = $this->createClientWithCredentials()->request('PATCH', 'http://api-test-php/api/products/5', [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'Product updated',
                'price' => 2000,
            ]
        ]);

        $this->assertResponseIsSuccessful();

        // Test if the updates have been saved correctly
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products/5');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products/5',
            '@type' => 'Product',
            'name' => 'Product updated',
            'price' => 2000,
        ]);
    }

    public function testDeleteProduct(): void
    {
        // Test delete product
        $response = $this->createClientWithCredentials()->request('DELETE', 'http://api-test-php/api/products/5');

        $this->assertResponseIsSuccessful();

        // Test if the product is not found
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products/5');

        $this->assertResponseStatusCodeSame(404);
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }

    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', 'http://api-test-php/api/login_check', ['json' => $body ?: [
            'username' => 'user1',
            'password' => 'pass1',
        ]]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->token = $data['token'];

        return $data['token'];
    }
}
