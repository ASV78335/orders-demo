<?php

namespace App\Tests\Controller;

use App\Person\Infrastructure\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;

class UnitControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetSelection(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'units/1');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testGetDetails(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/unit/' . $entities['unit']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateUnitGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/unit/' . $entities['unit']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateUnitPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/unit/create', [], [], [], json_encode([
            'name' => 'New test unit',
            'description' => 'New test unit description',
            'code' => '1000',
            'parent' => $entities['unit']->getUuid()->getStringValue()
        ]));

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateUnitGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/unit/' . $entities['unit']->getUuid()->getStringValue() . '/edit');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateUnitPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/unit/' . $entities['unit']->getUuid()->getStringValue() . '/edit', [], [], [], json_encode([
            'name' => 'New test unit',
            'description' => 'New test unit description',
            'code' => '1000',
            'parent' => $entities['unit']->getUuid()->getStringValue()
        ]));

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    public function testDelete(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('DELETE', '/unit/' . $entities['unit']->getUuid()->getStringValue() . '/delete');
        $response = $this->client->getResponse();

        $this->assertResponseRedirects('/');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
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
