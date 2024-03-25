<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\ResponseError;
use App\Unit\Application\Command\UnitCommandInteractor;
use App\Unit\Application\Command\UnitCreateCommand;
use App\Unit\Application\Command\UnitUpdateCommand;
use App\Unit\Application\Query\UnitDetails;
use App\Unit\Application\Query\UnitItem;
use App\Unit\Application\Query\UnitList;
use App\Unit\Application\Query\UnitQueryInteractor;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class ApiUnitController extends AbstractController
{
    public function __construct(
        private readonly UnitQueryInteractor   $queryInteractor,
        private readonly UnitCommandInteractor $commandInteractor
    )
    {

    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/units', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns units', attachables: [new Model(type: UnitList::class)])]
    public function getAll(): Response
    {
        return $this->json($this->queryInteractor->getAll($this->getUser()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/unit/{uuid}', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Return a unit', attachables: [new Model(type: UnitDetails::class)])]
    #[OA\Response(response: 404, description: 'Unit not found', attachables: [new Model(type: ResponseError::class)])]
    public function getDetails(Uuid $uuid): Response
    {
        return $this->json($this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122()));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/unit', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Create a unit', attachables: [new Model(type: UnitItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UnitCreateCommand::class)])]
    public function create(#[RequestBody] UnitCreateCommand $request): Response
    {
        $result = $this->commandInteractor->create($this->getUser(), $request);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/unit/{uuid}', methods: ['POST'])]
    #[OA\Response(response: 200, description: 'Update a unit', attachables: [new Model(type: UnitItem::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ResponseError::class)])]
    #[OA\Response(response: 404, description: 'Unit not found', attachables: [new Model(type: ResponseError::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UnitUpdateCommand::class)])]
    public function update(Uuid $uuid, #[RequestBody] UnitUpdateCommand $request): Response
    {
        $result = $this->commandInteractor->update($this->getUser(), $request, $uuid);
        return $this->json($result);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/api/v1/unit/{uuid}', methods: ['DELETE'])]
    #[OA\Response(response: 200, description: 'Remove a unit')]
    #[OA\Response(response: 404, description: 'Unit not found', attachables: [new Model(type: ResponseError::class)])]
    public function delete(Uuid $uuid): Response
    {
        $this->commandInteractor->delete($this->getUser(), $uuid);
        return $this->json(null);
    }
}
