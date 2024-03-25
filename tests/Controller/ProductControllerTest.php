<?php

namespace App\Tests\Controller;

use App\Repository\PersonRepository;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use Exception;

class ProductControllerTest extends AbstractControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetSelection(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', 'products/1');
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

        $this->client->request('GET', '/product/' . $entities['product']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateProductGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/product/' . $entities['product']->getUuid()->getStringValue() . '/view');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testCreateProductPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/product/create', [], [], [], json_encode([
            'name' => 'New test product',
            'description' => 'New test product description',
            'code' => '1000',
            'category' => $entities['category']->getUuid()->getStringValue(),
            'baseUnit' => $entities['unit']->getUuid()->getStringValue()
        ]));

        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateProductGet(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('GET', '/product/' . $entities['product']->getUuid()->getStringValue() . '/edit');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdateProductPost(): void
    {
        $entities = $this->createEntities();

        $this->client->loginUser($entities['person']);

        $this->client->request('POST', '/product/' . $entities['product']->getUuid()->getStringValue() . '/edit', [], [], [], json_encode([
            'name' => 'New test product',
            'description' => 'New test product description',
            'code' => '1000',
            'category' => $entities['category']->getUuid()->getStringValue(),
            'baseUnit' => $entities['unit']->getUuid()->getStringValue()
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

        $this->client->request('DELETE', '/product/' . $entities['product']->getUuid()->getStringValue() . '/delete');
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
