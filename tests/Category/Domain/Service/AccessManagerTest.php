<?php

namespace App\Tests\Category\Domain\Service;

use App\Category\Domain\Service\AccessManager;
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
        $category = MockUtils::createCategory();

        $response = $this->createAccessManager()->canView(null, $category);
        $this->assertFalse($response);
    }

    public function testCanViewAssertTrue(): void
    {
        $person = MockUtils::createPerson();
        $category = MockUtils::createCategory();

        $response = $this->createAccessManager()->canView($person, $category);
        $this->assertTrue($response);
    }

    private function createAccessManager(): AccessManager
    {
        return new AccessManager($this->provider);
    }

}
