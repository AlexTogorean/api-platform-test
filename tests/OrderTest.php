<?php


use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class OrderTest extends ApiTestCase
{
    private ?string $token = null;

    public function testGetOrders(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/orders',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
            'hydra:member' => [
                [
                    '@id' => '/api/orders/1',
                    '@type' => 'Order',
                    'amount' => 700,
                    'orderItems' => [
                        [
                            '@type' => 'OrderItem',
                            'quantity' => 1,
                            'product' => [
                                '@type' => 'Product',
                                'name' => 'Product 1',
                                'price' => 100,
                            ],
                        ],
                        [
                            '@type' => 'OrderItem',
                            'quantity' => 2,
                            'product' => [
                                '@type' => 'Product',
                                'name' => 'Product 3',
                                'price' => 300,
                            ],
                        ],
                    ],
                ],
            ]
        ]);
    }

    public function testGetOrder(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders/1');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/orders/1',
            '@type' => 'Order',
            'amount' => 700,
            'orderItems' => [
                [
                    '@type' => 'OrderItem',
                    'quantity' => 1,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 1',
                        'price' => 100,
                    ],
                ],
                [
                    '@type' => 'OrderItem',
                    'quantity' => 2,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 3',
                        'price' => 300,
                    ],
                ],
            ],
        ]);
    }

    public function testGetOrdersByProduct(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/products/1/orders');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/products/1/orders',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 3,
            'hydra:member' => [
                [
                    '@id' => '/api/orders/1',
                    '@type' => 'Order',
                    'amount' => 700,
                    'orderItems' => [
                        [
                            '@type' => 'OrderItem',
                            'quantity' => 1,
                            'product' => [
                                '@type' => 'Product',
                                'name' => 'Product 1',
                                'price' => 100,
                            ],
                        ],
                        [
                            '@type' => 'OrderItem',
                            'quantity' => 2,
                            'product' => [
                                '@type' => 'Product',
                                'name' => 'Product 3',
                                'price' => 300,
                            ],
                        ],
                    ],
                ],
            ]
        ]);
    }

    public function testCreateOrder(): void
    {
        // Test new order creation
        $response = $this->createClientWithCredentials()->request('POST', 'http://api-test-php/api/orders', [
            'json' => [
                'amount' => 300,
                'orderItems' => [
                    [
                        'product' => '/api/products/1',
                        'quantity' => 1,
                    ],
                    [
                        'product' => '/api/products/2',
                        'quantity' => 1,
                    ],
                ],
            ]
        ]);

        $this->assertResponseIsSuccessful();

        // Test if the new order exists in the list of orders
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders/5');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/orders/5',
            '@type' => 'Order',
            'amount' => 300,
            'orderItems' => [
                [
                    '@type' => 'OrderItem',
                    'quantity' => 1,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 1',
                        'price' => 100,
                    ],
                ],
                [
                    '@type' => 'OrderItem',
                    'quantity' => 1,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 2',
                        'price' => 200,
                    ],
                ],
            ],
        ]);
    }

    public function testUpdateOrder(): void
    {
        // Test order update
        $response = $this->createClientWithCredentials()->request('PATCH', 'http://api-test-php/api/orders/5', [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'amount' => 500,
                'orderItems' => [
                    [
                        'product' => '/api/products/1',
                        'quantity' => 1,
                    ],
                    [
                        'product' => '/api/products/2',
                        'quantity' => 2,
                    ],
                ],
            ]
        ]);

        $this->assertResponseIsSuccessful();

        // Test if the updates have been saved correctly
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders/5');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/orders/5',
            '@type' => 'Order',
            'amount' => 500,
            'orderItems' => [
                [
                    '@type' => 'OrderItem',
                    'quantity' => 1,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 1',
                        'price' => 100,
                    ],
                ],
                [
                    '@type' => 'OrderItem',
                    'quantity' => 2,
                    'product' => [
                        '@type' => 'Product',
                        'name' => 'Product 2',
                        'price' => 200,
                    ],
                ],
            ],
        ]);
    }

    public function testDeleteOrder(): void
    {
        // Test order delete
        $response = $this->createClientWithCredentials()->request('DELETE', 'http://api-test-php/api/orders/5');

        $this->assertResponseIsSuccessful();

        // Test if the order is not found
        $response = $this->createClientWithCredentials()->request('GET', 'http://api-test-php/api/orders/5');

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
