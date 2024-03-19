<?php

namespace App\Tests;

use App\Category\Application\Command\CategoryCreateCommand;
use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use App\Category\Domain\Category;
use App\Order\Domain\Order;
use App\OrderEntry\Domain\OrderEntry;
use App\Person\Domain\Person;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Query\ProductDetails;
use App\Product\Application\Query\ProductItem;
use App\Product\Domain\Product;
use App\Unit\Application\Command\UnitCreateCommand;
use App\Unit\Application\Query\UnitDetails;
use App\Unit\Application\Query\UnitItem;
use App\Unit\Domain\Unit;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class MockUtils
{

    public static function createCategory(): Category
    {
        $slug = 'Test-category';
        $parent = null;
        $categoryRequestCreate = (new CategoryCreateCommand())
            ->setName('Test category')
            ->setDescription('Test category description')
            ->setCode('01')
            ->setParent(null);
        $values = compact('parent', 'slug');

        return Category::fromCreateRequest($categoryRequestCreate, $values);
    }

    public static function createCategoryItem(): CategoryItem
    {
        return (new CategoryItem())
            ->setUuid(Uuid::v4())
            ->setName('Test category')
            ->setSlug('Test-category')
            ->setDescription('Test category description')
            ->setCode('01')
            ->setParentName(null)
        ;
    }

    public static function createCategoryDetails(?CategoryItem $parent): CategoryDetails
    {
        return (new CategoryDetails())
            ->setUuid(Uuid::v4())
            ->setName('Test category')
            ->setSlug('Test-category')
            ->setDescription('Test category description')
            ->setCode('01')
            ->setParent($parent)
            ;
    }

    public static function createUnit(): Unit
    {
        $slug = 'Test-unit';

        $unitRequestCreate = (new UnitCreateCommand())
            ->setName('Test unit')
            ->setDescription('')
            ->setCode('1100')
        ;
        $values = compact('slug');

        return Unit::fromCreateRequest($unitRequestCreate, $values);
    }

    public static function createUnitItem(): UnitItem
    {
        return (new UnitItem())
            ->setUuid(Uuid::v4())
            ->setName('Test unit')
            ->setSlug('Test-unit')
            ->setDescription('')
            ->setCode('1100')
        ;
    }

    public static function createUnitDetails(): UnitDetails
    {
        return (new UnitDetails())
            ->setUuid(Uuid::v4())
            ->setName('Test unit')
            ->setSlug('Test-unit')
            ->setDescription('')
            ->setCode('1100')
            ;
    }

    /**
     * @param Category $category
     * @param Unit $baseUnit
     * @return Product
     */
    public static function createProduct(Category $category, Unit $baseUnit): Product
    {
        $slug = 'Test-product';
        $values = compact('category', 'baseUnit', 'slug');
        $productRequestCreate = (new ProductCreateCommand())
            ->setName('Test product')
            ->setDescription('Test product description')
            ->setCode('1100')
        ;
        return Product::fromCreateRequest($productRequestCreate, $values);
    }

    public static function createProductItem(): ProductItem
    {
        return (new ProductItem())
            ->setUuid(Uuid::v4())
            ->setName('Test product')
            ->setSlug('Test-product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategoryName('Test category')
            ->setBaseUnitName('Test unit');
    }

    public static function createProductDetails(CategoryItem $categoryItem, UnitItem $unitItem): ProductDetails
    {
        return (new ProductDetails())
            ->setUuid(Uuid::v4())
            ->setName('Test product')
            ->setSlug('Test-product')
            ->setDescription('Test product description')
            ->setCode('1100')
            ->setCategory($categoryItem)
            ->setBaseUnit($unitItem)
        ;
    }

    public static function createPerson($currentContragent = null): Person
    {
        return (new Person())
            ->setUuid(Uuid::v4())
            ->setName('Test person')
            ->setEmail('test@email.com')
            ->setPassword('123')
            ->setPhone(null)
            ->setCurrentContragent($currentContragent)
            ->setConfirmedAt(null);
    }

    public static function createOrder(Person $person, Shop $shop): Record
    {
        return (new Order())
            ->setUuid(Uuid::v4())
            ->setNumber('11')
            ->setShop($shop)
            ->setStatus(0)
            ->setTotalNds(100)
            ->setTotalSum(500)
            ->setDeliverAt(new DateTimeImmutable('2023-10-01'))
            ->setCreatedPerson($person)
        ;
    }

    public static function createOrderEntry(Order $order, Product $product, Unit $unit, Person $createdPerson): OrderEntry
    {
        return (new OrderEntry())
            ->setUuid(Uuid::v4())
            ->setOrder($order)
            ->setProduct($product)
            ->setUnit($unit)
            ->setCount(5)
            ->setPrice(100)
            ->setNds(100)
            ->setCreatedPerson($createdPerson)
        ;
    }

}
