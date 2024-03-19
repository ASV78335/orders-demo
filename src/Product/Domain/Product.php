<?php

namespace App\Product\Domain;

use App\Category\Domain\Category;
use App\Image\Domain\Image;
use App\ProductCharacteristic\Domain\ProductCharacteristic;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Product\Application\Query\ProductDetails;
use App\Product\Application\Query\ProductItem;
use App\Product\Domain\ValueObject\ProductUuid;
use App\Unit\Domain\Unit;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(columnPrefix: false)]
    private ProductUuid $uuid;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $status = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $baseUnit = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $baseImage = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductCharacteristic::class)]
    private Collection $productCharacteristics;


    private function __construct()
    {
        $this->images = new ArrayCollection();
        $this->productCharacteristics = new ArrayCollection();
    }


    public static function fromCreateRequest(ProductCreateCommand $model, array $values): self
    {
        extract($values);
        $product = new self();
        $product
            ->setUuid(new ProductUuid())
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setCategory($category)
            ->setBaseUnit($baseUnit)
            ->setCreatedAt(new DateTimeImmutable())
        ;
        return $product;
    }

    public static function fromUpdateRequest(Product $product, ProductUpdateCommand $model, array $values): self
    {
        extract($values);
        $product
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setCategory($category)
            ->setBaseUnit($baseUnit)
            ->setUpdatedAt(new DateTimeImmutable())
        ;
        return $product;
    }

    public function toResponseItem(): ProductItem
    {
        return (new ProductItem())
            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ->setCategoryName($this->getCategory()->getName())
            ->setBaseUnitName($this->getBaseUnit()->getName())
        ;
    }

    public function toResponseDetails(): ProductDetails
    {
        return (new ProductDetails())
            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ->setCategory($this->getCategory()->toResponseItem())
            ->setBaseUnit($this->getBaseUnit()->toResponseItem())
        ;
    }

    public function makeDeleted(): void
    {
        $this->setDeletedAt(new DateTimeImmutable());
    }

    public function getUuid(): ProductUuid
    {
        return $this->uuid;
    }

    private function setUuid(ProductUuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    private function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    private function getSlug(): string
    {
        return $this->slug;
    }

    private function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    private function getDescription(): ?string
    {
        return $this->description;
    }

    private function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    private function getCode(): ?string
    {
        return $this->code;
    }

    private function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    private function getCategory(): ?Category
    {
        return $this->category;
    }

    private function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    private function getBaseUnit(): ?Unit
    {
        return $this->baseUnit;
    }

    private function setBaseUnit(?Unit $baseUnit): self
    {
        $this->baseUnit = $baseUnit;

        return $this;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;

    }

    private function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;

    }

    private function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;

    }

}
