<?php

namespace App\Tests\Product\Domain\Service;

use App\Product\Domain\Service\AccessManager;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class AccessManagerTest extends AbstractTestCase
{

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
        $response = $this->createAccessManager()->canView(null);
        $this->assertFalse($response);
    }

    public function testCanViewAssertTrue(): void
    {
        $person = MockUtils::createPerson();

        $response = $this->createAccessManager()->canView($person);
        $this->assertTrue($response);
    }

    private function createAccessManager(): AccessManager
    {
        return new AccessManager();
    }

}
