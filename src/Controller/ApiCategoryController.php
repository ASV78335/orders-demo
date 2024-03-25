<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Category\Application\Command\CategoryCommandInteractor;
use App\Category\Application\Command\CategoryCreateCommand;
use App\Category\Application\Command\CategoryUpdateCommand;
use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use App\Category\Application\Query\CategoryList;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\Model\ResponseError;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class ApiCategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryQueryInteractor   $queryInteractor,
        private readonly CategoryCommandInteractor $commandInteractor
    )
    {

    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/categories', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns all categories', attachables: [new Model(type: CategoryList::class)])]
    public function getAll(): Response
    {
        return $this->json($this->queryInteractor->getAll($this->getUser()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/category/{uuid}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Return a category', attachables: [new Model(type: CategoryDetails::class)])]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ResponseError::class)])]
    public function getDetails(Uuid $uuid): Response
    {
        return $this->json($this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/category', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Create a category', attachables: [new Model(type: CategoryItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CategoryCreateCommand::class)])]
    public function create(#[RequestBody] CategoryCreateCommand $request): Response
    {
        $result = $this->commandInteractor->create($this->getUser(), $request);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/category/{uuid}', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Update a category', attachables: [new Model(type: CategoryItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CategoryUpdateCommand::class)])]
    public function update(Uuid $uuid, #[RequestBody] CategoryUpdateCommand $request): Response
    {
        $result = $this->commandInteractor->update($this->getUser(), $request, $uuid);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/category/{uuid}', methods: ['DELETE'])]
    #[OA\Response(response: 200, description: 'Remove a category')]
    #[OA\Response(response: 404, description: 'Category not found', attachables: [new Model(type: ResponseError::class)])]
    public function delete(Uuid $uuid): Response
    {
        $this->commandInteractor->delete($this->getUser(), $uuid);
        return $this->json(null);
    }
}
