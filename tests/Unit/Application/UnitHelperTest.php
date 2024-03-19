<?php

namespace App\Tests\Unit\Application;

use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use App\Unit\Application\Query\UnitDetails;
use App\Unit\Application\UnitEntityProvider;
use App\Unit\Application\UnitHelper;

class UnitHelperTest extends AbstractTestCase
{
    private readonly UnitEntityProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->createMock(UnitEntityProvider::class);
    }

    public function testGetInstanceName(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals('Unit', $helper->getInstanceName());
    }

    public function testGetNewDetails(): void
    {
        $helper = $this->createHelper();

        $this->assertEquals(new UnitDetails(), $helper->getNewDetails());
    }

    public function testGetRequestOptions(): void
    {
        $entities = $this->createEntities();

        $this->assertEquals([] ,$this->createHelper()->getRequestOptions($entities['person']));
    }

    private function createEntities(): array
    {
        $unit = MockUtils::createUnit();
        $person = MockUtils::createPerson();

        return ['unit' => $unit, 'person' => $person];
    }

    private function createHelper(): UnitHelper
    {
        return new UnitHelper(
            $this->provider
        );
    }
}
