<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ValidationBundle\Tests\Functional\Validation;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * This test class is testing sulu validation.
 */
class ValidationTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Test calling a route without validation.
     */
    public function testNoValidation()
    {
        $this->client->request('GET', '/no-validation');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test validation success on GET request.
     */
    public function testGetValidationSuccess()
    {
        $this->client->request('GET', '/get-validation', ['locale' => 'en']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests validation error on GET request.
     */
    public function testGetValidationError()
    {
        $this->client->request('GET', '/get-validation');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(1, $responseContent);
        $this->assertResponseContainsProperties($responseContent, ['locale']);
    }

    /**
     * Test validation success on POST request.
     */
    public function testPostValidationSuccess()
    {
        $this->client->request(
            'POST',
            '/post-validation',
            [
                'locale' => 'en',
                'name' => 'test',
                'attributes' => [
                    ['id' => 2],
                ],
            ]
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests validation error on POST request.
     */
    public function testPostValidationError()
    {
        $this->client->request('POST', '/post-validation');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseContent);
        $this->assertResponseContainsProperties($responseContent, ['name', 'attributes']);
    }

    public function testValidationOfSchemaWithInlineRefs()
    {
        $data = [
            'billingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'zip' => 'ABC1234',
                'country' => 'Testcountry',
            ],
            'shippingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'zip' => 'ABC1234',
                'country' => 'Testcountry',
            ],
        ];

        $this->client->request('POST', '/schema-with-inline-refs', $data);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testValidationOfSchemaWithRefs()
    {
        $data = [
            'billingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'zip' => 'ABC1234',
                'country' => 'Testcountry',
            ],
            'shippingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'zip' => 'ABC1234',
                'country' => 'Testcountry',
            ],
        ];

        $this->client->request('POST', '/schema-with-refs', $data);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     *  Tests if missing shipping address and missing zip of billing address are detected when using inline refs.
     */
    public function testValidationOfSchemaWithInlineRefsAndErrors()
    {
        $data = [
            'billingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'country' => 'Testcountry',
            ],
        ];

        $this->client->request('POST', '/schema-with-inline-refs', $data);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseContainsProperties($responseContent, ['shippingAddress', 'billingAddress.zip']);
    }

    /**
     *  Tests if missing shipping address and missing zip of billing address are detected when using refs.
     */
    public function testValidationOfSchemaWithRefsAndErrors()
    {
        $data = [
            'billingAddress' => [
                'street' => 'Teststreet',
                'city' => 'Testcity',
                'country' => 'Testcountry',
            ],
        ];

        $this->client->request('POST', '/schema-with-refs', $data);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertResponseContainsProperties($responseContent, ['shippingAddress', 'billingAddress.zip']);
    }

    /**
     * Tests if response contains expected properties.
     *
     * @param array $responseContent
     * @param array $properties
     */
    public function assertResponseContainsProperties(array $responseContent, array $properties)
    {
        foreach ($responseContent as $index => $content) {
            $this->assertContains($content['property'], $properties);
            unset($responseContent[$index]);
        }
    }
}
