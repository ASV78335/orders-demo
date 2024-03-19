<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractTestCase extends TestCase
{
    protected function setPropertyValue(object $entity, string $propertyName, $value): void
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }
}
