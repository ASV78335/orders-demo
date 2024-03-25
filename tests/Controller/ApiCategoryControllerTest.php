<?php

namespace App\Tests\Controller;

use App\Repository\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;
use JsonException;

class ApiCategoryControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetAll(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'api/v1/categories');
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
                        'required' => ['uuid', 'name', 'slug', 'description', 'code', 'parentName'],
                        'properties' => [
                            'uuid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'code' => ['type' => 'string'],
                            'parentName' => ['string', 'null']
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

        $this->client->request('GET', '/api/v1/category/' . $entities['category']->getUuid()->getStringValue());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'parent'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'parent' => [
                    'type' => ['object', 'null'],
                    'required' => ['uuid', 'name', 'slug'],
                    'properties' => [
                        'uuid' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'slug' => ['type' => 'string']
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

        $this->client->request('POST', '/api/v1/category', [], [], [], json_encode([
            'name' => 'New test category',
            'description' => 'New test category description',
            'code' => '1000',
            'parent' => $entities['category']->getUuid()->getStringValue()
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'parentName'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'parentName' => ['type' => 'string', 'null']
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

        $this->client->request('POST', '/api/v1/category/' . $entities['category']->getUuid()->getStringValue(), [], [], [], json_encode([
            'name' => 'New test category',
            'description' => 'New test category description',
            'code' => '1000',
            'parent' => $entities['category']->getUuid()->getStringValue()
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code', 'parentName'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
                'parentName' => ['type' => 'string', 'null']
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

        $this->client->request('DELETE', '/api/v1/category/' . $entities['category']->getUuid()->getStringValue());

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    private function createEntities(): array
    {
        $category = MockUtils::createCategory();

        $this->personRepository = self::getContainer()->get(PersonRepository::class);
        $person = $this->personRepository->findOneByEmail('ivanov@mail.ru');
        $person->setStatus($_ENV['ADMIN_STATUS']);

        $this->em->persist($category);
        $this->em->flush();

        return ['category' => $category, 'person' => $person];
    }

}