<?php

namespace App\Category\Domain;

use App\Category\Application\Command\CategoryCreateCommand;
use App\Category\Application\Command\CategoryUpdateCommand;
use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use App\Category\Domain\ValueObject\CategoryUuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(columnPrefix: false)]
    private CategoryUuid $uuid;

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

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;


    private function __construct()
    {
    }


    public static function fromCreateRequest(CategoryCreateCommand $model, array $values): self
    {
        extract($values);
        $category = new self();
        $category
            ->setUuid(new CategoryUuid())
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setParent($parent)
            ->setCreatedAt(new DateTimeImmutable())
        ;
        return $category;
    }

    public static function fromUpdateRequest(Category $category, CategoryUpdateCommand $model, array $values): self
    {
        extract($values);

        $category
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setParent($parent)
            ->setUpdatedAt(new DateTimeImmutable())
        ;
        return $category;
    }

    public function toResponseItem(): CategoryItem
    {
        return (new CategoryItem())

            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ->setParentName($this->getParent()?->getName())
            ;
    }

    public function toResponseDetails(): CategoryDetails
    {
        return (new CategoryDetails())

            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ->setParent($this->getParent()?->toResponseItem())
            ;
    }

    public function makeDeleted(): void
    {
        $this->setDeletedAt(new DateTimeImmutable());
    }

    public function getUuid(): ?CategoryUuid
    {
        return $this->uuid;
    }

    private function setUuid(CategoryUuid $uuid): self
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

    private function getSlug(): ?string
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

    private function getParent(): ?self
    {
        return $this->parent;
    }

    private function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    private function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;

    }

    private function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;

    }

    private function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    private function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;

    }

}
