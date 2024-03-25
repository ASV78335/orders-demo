<?php

namespace App\Tests\Controller;

use App\Repository\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;

class CategoryControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetSelection(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'categories/1');
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

        $this->client->request('GET', '/category/' . $entities['category']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateCategoryGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/category/' . $entities['category']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateCategoryPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/category/create', [], [], [], json_encode([
            'name' => 'New test category',
            'description' => 'New test category description',
            'code' => '1000',
            'parent' => $entities['category']->getUuid()->getStringValue()
        ]));

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateCategoryGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/category/' . $entities['category']->getUuid()->getStringValue() . '/edit');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateCategoryPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/category/' . $entities['category']->getUuid()->getStringValue() . '/edit', [], [], [], json_encode([
            'name' => 'New test category',
            'description' => 'New test category description',
            'code' => '1000',
            'parent' => $entities['category']->getUuid()->getStringValue()
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

        $this->client->request('DELETE', '/category/' . $entities['category']->getUuid()->getStringValue() . '/delete');
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
        $category = MockUtils::createCategory();

        $this->personRepository = self::getContainer()->get(PersonRepository::class);
        $person = $this->personRepository->findOneByEmail('ivanov@mail.ru');
        $person->setStatus($_ENV['ADMIN_STATUS']);

        $this->em->persist($category);
        $this->em->flush();

        return ['category' => $category, 'person' => $person];
    }

}
