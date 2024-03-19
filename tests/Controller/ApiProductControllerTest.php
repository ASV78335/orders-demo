<?php

namespace App\Tests\Controller;

use App\Person\Infrastructure\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;
use JsonException;

class ApiProductControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetAll(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'api/v1/products');
        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['uuid', 'name', 'slug', 'description', 'code', 'categoryName', 'baseUnitName'],
                        'properties' => [
                            'uuid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'code' => ['type' => 'string'],
                            'categoryName' => ['type' => 'string'],
                            'baseUnitName' => ['type' => 'string']
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function testGetDetails(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/api/v1/product/' . $entities['product']->getUuid()->getStringValue());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'category', 'baseUnit'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'category' => [
                    'type' => 'object',
                    'required' => ['uuid', 'name', 'slug'],
                    'properties' => [
                        'uuid' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'slug' => ['type' => 'string']
                    ],
                ],
                'baseUnit' => [
                    'type' => 'object',
                    'required' => ['uuid', 'name', 'slug', 'code'],
                    'properties' => [
                        'uuid' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'slug' => ['type' => 'string'],
                        'code' => ['type' => 'string']
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function testCreate(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/api/v1/product', [], [], [], json_encode([
            'name' => 'New test product',
            'description' => 'New test product description',
            'code' => '1000',
            'category' => $entities['category']->getUuid()->getStringValue(),
            'baseUnit' => $entities['unit']->getUuid()->getStringValue()
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'categoryName', 'baseUnitName'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'categoryName' => ['type' => 'string'],
                'baseUnitName' => ['type' => 'string']
            ]
        ]);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function testUpdate(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/api/v1/product/' . $entities['product']->getUuid()->getStringValue(), [], [], [], json_encode([
            'name' => 'New test product',
            'description' => 'New test product description',
            'code' => '1000',
            'category' => $entities['category']->getUuid()->getStringValue(),
            'baseUnit' => $entities['unit']->getUuid()->getStringValue()
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'categoryName', 'baseUnitName'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'categoryName' => ['type' => 'string'],
                'baseUnitName' => ['type' => 'string']
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function testDelete(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('DELETE', '/api/v1/product/' . $entities['product']->getUuid()->getStringValue());

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);

        $this->personRepository = self::getContainer()->get(PersonRepository::class);
        $person = $this->personRepository->findOneByEmail('ivanov@mail.ru');
        $person->setStatus($_ENV['ADMIN_STATUS']);

        $this->em->persist($category);
        $this->em->persist($unit);
        $this->em->persist($product);
        $this->em->flush();

        return ['category' => $category, 'unit' => $unit, 'product' => $product, 'person' => $person];
    }

}