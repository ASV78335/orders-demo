<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\Product\ProductList;
use App\Model\ResponseError;
use App\Product\Application\Command\ProductCommandInteractor;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Product\Application\Query\ProductDetails;
use App\Product\Application\Query\ProductItem;
use App\Product\Application\Query\ProductQueryInteractor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class ApiProductController extends AbstractController
{

    public function __construct(
        private readonly ProductQueryInteractor   $queryInteractor,
        private readonly ProductCommandInteractor $commandInteractor
    )
    {
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/api/v1/products', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns all products', attachables: [new Model(type: ProductList::class)])]
    public function getAll(): Response
    {
        return $this->json($this->queryInteractor->getAll($this->getUser()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/api/v1/product/{uuid}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns product detail information', attachables: [new Model(type: ProductDetails::class)])]
    #[OA\Response(response: 404, description: 'Product not found', attachables: [new Model(type: ResponseError::class)])]
    public function getDetails(Uuid $uuid): Response
    {
        return $this->json($this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/product', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Create a product', attachables: [new Model(type: ProductItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: ProductCreateCommand::class)])]
    public function create(#[RequestBody] ProductCreateCommand $request): Response
    {
        $result = $this->commandInteractor->create($this->getUser(), $request);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/product/{uuid}', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Update a product', attachables: [new Model(type: ProductItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\Response(response: 404, description: 'Product not found', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: ProductUpdateCommand::class)])]
    public function update(Uuid $uuid, #[RequestBody] ProductUpdateCommand $request): Response
    {
        $result = $this->commandInteractor->update($this->getUser(), $request, $uuid);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/product/{uuid}', methods: ['DELETE'])]
    #[OA\Response(response: 200, description: 'Remove a product')]
    #[OA\Response(response: 404, description: 'Product not found', attachables: [new Model(type: ResponseError::class)])]
    public function delete(Uuid $uuid): Response
    {
        $this->commandInteractor->delete($this->getUser(), $uuid);
        return $this->json(null);
    }

}
