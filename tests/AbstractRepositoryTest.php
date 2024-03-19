<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{
    protected ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = self::getContainer()->get('doctrine.orm.entity_manager');
    }
    
    protected function getRepositoryForEntity(string $entityClass): mixed
    {
        return $this->em->getRepository($entityClass);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em = null;
    }

    protected function setPropertyValue(object $entity, string $propertyName, $value): void
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }

}