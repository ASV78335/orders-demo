<?php

namespace App\Shared\Application;

use App\Account\Domain\Exception\AccountNotFoundException;
use App\Address\Domain\Exception\AddressNotFoundException;
use App\Category\Domain\Exception\CategoryNotFoundException;
use App\Category\Infrastructure\CategoryRepository;
use App\Exception\Contract\ContractNotFoundException;
use App\Exception\Contragent\ContragentNotFoundException;
use App\Exception\ContragentEmployee\ContragentEmployeeNotFoundException;
use App\Exception\ContragentGroup\ContragentGroupNotFoundException;
use App\Exception\Person\PersonNotFoundException;
use App\Exception\PersonRight\PersonRightNotFoundException;
use App\Exception\PriceList\PriceListNotFoundException;
use App\Exception\PriceListEntry\PriceListEntryNotFoundException;
use App\Exception\Record\RecordNotFoundException;
use App\Exception\RecordEntry\RecordEntryNotFoundException;
use App\Exception\RepositoryNotSupportedException;
use App\Exception\Shop\ShopNotFoundException;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\ProductRepository;
use App\Repository\AbstractRepository;
use App\Unit\Domain\Exception\UnitNotFoundException;
use App\Unit\Infrastructure\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;

class EntityProvider // implements EntityProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    private function getRepository(string $entityName)
    {
        switch ($entityName) {
            case 'App\Product\Domain\Product':
                $repository = new ProductRepository($this->em);
                break;
            case 'App\Category\Domain\Category':
                $repository = new CategoryRepository($this->em);
                break;
            case 'App\Unit\Domain\Unit':
                $repository = new UnitRepository($this->em);
                break;
            default: $repository = $this->em->getRepository($entityName);
        }

        return $repository;
    }

    public function getEntityByUuid(string $entityName, string $uuid)
    {
        $repository = $this->getRepository($entityName);
//        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        if (!$repository->existByUuid($uuid)) {

            switch ($entityName) {
                case 'App\Account\Domain\Account':
                    throw new AccountNotFoundException();
                    break;
                case 'App\Address\Domain\Address':
                    throw new AddressNotFoundException($uuid);
                    break;
                case 'App\Category\Domain\Category':
                    throw new CategoryNotFoundException();
                    break;
                case 'App\Entity\Contract':
                    throw new ContractNotFoundException();
                    break;
                case 'App\Entity\ContragentEmployee':
                    throw new ContragentEmployeeNotFoundException();
                    break;
                case 'App\Entity\ContragentGroup':
                    throw new ContragentGroupNotFoundException();
                    break;
                case 'App\Entity\Contragent':
                    throw new ContragentNotFoundException();
                    break;
                case 'App\Entity\Person':
                    throw new PersonNotFoundException();
                    break;
                case 'App\Entity\PersonRight':
                    throw new PersonRightNotFoundException();
                    break;
                case 'App\Entity\PriceListEntry':
                    throw new PriceListEntryNotFoundException();
                    break;
                case 'App\Entity\PriceList':
                    throw new PriceListNotFoundException();
                    break;
                case 'App\Product\Domain\Product':
                    throw new ProductNotFoundException();
                    break;
                case 'App\Entity\RecordEntry':
                    throw new RecordEntryNotFoundException();
                    break;
                case 'App\Entity\Record':
                    throw new RecordNotFoundException();
                    break;
                case 'App\Entity\Shop':
                    throw new ShopNotFoundException();
                    break;
                case 'App\Unit\Domain\Unit':
                    throw new UnitNotFoundException();
                    break;
            }
        }

        return $repository->getByUuid($uuid);
    }

    public function getNotDeletedEntitiesSortedByName(string $entityName): array
    {
        $repository = $this->getRepository($entityName);

//        if (!$repository instanceof AbstractRepository) {
//            throw new RepositoryNotSupportedException($repository->getClassName());
//        }

        if (!method_exists($repository, 'findNotDeletedSortedByName')) {
//            throw new MethodNotSupportedException('findNotDeletedSortedByName');
            return $repository->findBy(['deletedAt' => null]);
        }

        return $repository->findNotDeletedSortedByName();
    }

    public function getNotDeletedEntitiesByPage(string $entityName, int $offset, int $count): array
    {
        $repository = $this->getRepository($entityName);

//        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        if (!method_exists($repository, 'findForPagination')) {
//            throw new MethodNotSupportedException('findForPagination');
            return $repository->getNotDeletedByPage($offset, $count);
        };

        return $repository->getNotDeletedByPage($offset, $count);
    }

    public function getEntitiesByField(string $entityName, string $field, object $value): array
    {
        $repository = $this->getRepository($entityName);

        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        return $repository->findBy([$field => $value]);
    }

    public function getNotDeletedEntitiesByField(string $entityName, string $field, object $value): array
    {
        $repository = $this->getRepository($entityName);

//        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        return $repository->findBy([$field => $value, 'deletedAt' => null]);
    }

    public function getEntitiesByFields(string $entityName, array $fields, object $value): array
    {
        $repository = $this->getRepository($entityName);

//        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        $result = [];
        foreach ($fields as $field) {
            $result = array_merge($result, $repository->findBy([$field => $value]));
        }

        return $result;
    }

    public function getNotDeletedEntitiesByFields(string $entityName, array $fields, object $value): array
    {
        $repository = $this->getRepository($entityName);

//        if (!$repository instanceof AbstractRepository) throw new RepositoryNotSupportedException($repository->getClassName());

        $result = [];
        foreach ($fields as $field) {
            $result = array_merge($result, $repository->findBy([$field => $value, 'deletedAt' => null]));
        }

        return $result;
    }

}
