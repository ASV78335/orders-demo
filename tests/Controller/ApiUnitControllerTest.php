<?php

namespace App\Tests\Controller;

use App\Person\Infrastructure\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;
use JsonException;

class ApiUnitControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetAll(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'api/v1/units');
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
                        'required' => ['uuid', 'name', 'slug', 'description', 'code'],
                        'properties' => [
                            'uuid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'code' => ['type' => 'string']
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

        $this->client->request('GET', '/api/v1/unit/' . $entities['unit']->getUuid()->getStringValue());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string'],
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

        $this->client->request('POST', '/api/v1/unit', [], [], [], json_encode([
            'name' => 'New test unit',
            'description' => 'New test unit description',
            'code' => '1000'
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string']
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

        $this->client->request('POST', '/api/v1/unit/' . $entities['unit']->getUuid()->getStringValue(), [], [], [], json_encode([
            'name' => 'New test unit',
            'description' => 'New test unit description',
            'code' => '1000'
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent(), null, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => [
                'uuid', 'name', 'slug', 'description', 'code'
            ],
            'properties' => [
                'uuid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'code' => ['type' => 'string']
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

        $this->client->request('DELETE', '/api/v1/unit/' . $entities['unit']->getUuid()->getStringValue());

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    private function createEntities(): array
    {
        $unit = MockUtils::createUnit();

        $this->personRepository = self::getContainer()->get(PersonRepository::class);
        $person = $this->personRepository->findOneByEmail('ivanov@mail.ru');
        $person->setStatus($_ENV['ADMIN_STATUS']);

        $this->em->persist($unit);
        $this->em->flush();

        return ['unit' => $unit, 'person' => $person];
    }

}