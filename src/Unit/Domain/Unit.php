<?php

namespace App\Unit\Domain;

use App\Unit\Application\Command\UnitCreateCommand;
use App\Unit\Application\Command\UnitUpdateCommand;
use App\Unit\Application\Query\UnitDetails;
use App\Unit\Application\Query\UnitItem;
use App\Unit\Domain\ValueObject\UnitUuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
class Unit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Embedded(columnPrefix: false)]
    private UnitUuid $uuid;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;


    private function __construct()
    {
    }


    public static function fromCreateRequest(UnitCreateCommand $model, array $values): self
    {
        extract($values);
        $unit = new self();
        $unit
            ->setUuid(new UnitUuid())
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setCreatedAt(new DateTimeImmutable())
        ;
        return $unit;
    }

    public static function fromUpdateRequest(Unit $unit, UnitUpdateCommand $model, array $values): self
    {
        extract($values);

        $unit
            ->setName($model->getName())
            ->setSlug($slug)
            ->setDescription($model->getDescription())
            ->setCode($model->getCode())
            ->setUpdatedAt(new DateTimeImmutable())
        ;
        return $unit;
    }

    public function toResponseItem(): UnitItem
    {
        return (new UnitItem())

            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ;
    }

    public function toResponseDetails(): UnitDetails
    {
        return (new UnitDetails())

            ->setUuid($this->getUuid()->getStringValue())
            ->setName($this->getName())
            ->setSlug($this->getSlug())
            ->setDescription($this->getDescription())
            ->setCode($this->getCode())
            ;
    }

    public function makeDeleted(): void
    {
        $this->setDeletedAt(new DateTimeImmutable());
    }

    public function getUuid(): ?UnitUuid
    {
        return $this->uuid;
    }

    private function setUuid(UnitUuid $uuid): self
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

    private function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    private function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    private function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    private function setDeletedAt(?DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
