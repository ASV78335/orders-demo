<?php

namespace App\Tests\Product\Domain\Service;

use App\Product\Domain\Service\AccessManager;
use App\Shared\Application\EntityProvider;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class AccessManagerTest extends AbstractTestCase
{
    private readonly EntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->createMock(EntityProvider::class);
    }

    public function testCanEditAssertFalse(): void
    {
        $person = MockUtils::createPerson();

        $response = $this->createAccessManager()->canEdit($person);
        $this->assertFalse($response);
    }

    public function testCanEditAssertTrue(): void
    {
        $person = MockUtils::createPerson();
        $this->setPropertyValue($person, 'status', $_ENV['ADMIN_STATUS']);

        $response = $this->createAccessManager()->canEdit($person);
        $this->assertTrue($response);
    }

    public function testCanViewAssertFalse(): void
    {
        $entities = $this->createEntities();

        $response = $this->createAccessManager()->canView(null, $entities['product']);
        $this->assertFalse($response);
    }

    public function testCanViewAssertTrue(): void
    {
        $entities = $this->createEntities();

        $response = $this->createAccessManager()->canView($entities['person'], $entities['product']);
        $this->assertTrue($response);
    }

    private function createAccessManager(): AccessManager
    {
        return new AccessManager($this->provider);
    }


    private function createEntities(): array
    {
        $category = MockUtils::createCategory();
        $unit = MockUtils::createUnit();
        $product = MockUtils::createProduct($category, $unit);
        $person = MockUtils::createPerson();

        return ['category' => $category, 'unit' => $unit, 'person' => $person, 'product' => $product];
    }
}
