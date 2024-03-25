<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\ControllerHelper\CommandDecorator;
use App\ControllerHelper\ControllerHelper;
use App\ControllerHelper\DecoratorFactory;
use App\Unit\Application\Command\UnitCommandInteractor;
use App\Unit\Application\Command\UnitCreateCommand;
use App\Unit\Application\Command\UnitUpdateCommand;
use App\Unit\Application\Query\UnitQueryInteractor;
use App\Unit\Application\UnitHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class UnitController extends AbstractController
{
    private readonly CommandDecorator  $commandDecorator;

    public function __construct(
        private readonly ControllerHelper      $controllerHelper,
        private readonly UnitHelper            $unitHelper,
        private readonly UnitQueryInteractor   $queryInteractor,
        private readonly UnitCommandInteractor $commandInteractor,
        private readonly DecoratorFactory      $factory
    )
    {
        $this->commandDecorator = $this->factory->createCommandDecorator($this->commandInteractor, $this->queryInteractor, $this->unitHelper);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/units/{page}', name: 'app_units')]
    public function getSelection(Request $request, int $page): Response
    {
        $pagination = $this->controllerHelper->getPagination($this->unitHelper, (int)$request->cookies->get('entitiesForPage'), $page);
        $units = $this->queryInteractor->getSelection($this->getUser(), (int)$pagination['offset'], $request->cookies->get('entitiesForPage'))->getItems();
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/index.html.twig', array_merge($environment, $pagination, ['units' => $units]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/{uuid}/view', name: 'app_unit_view')]
    public function getDetails(Request $request, Uuid $uuid): Response
    {
        $unit = $this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122());
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/view.html.twig', array_merge($environment, ['unit' => $unit]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/create', name: 'app_unit_create_get', methods: ['GET'])]
    public function createUnitGet(Request $request): Response
    {
        $formData = $this->controllerHelper->getNewFormData($this->getUser(), $this->unitHelper);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/create', name: 'app_unit_create_post', methods: ['POST'])]
    public function createUnitPost(#[RequestBody] UnitCreateCommand $request): Response
    {
        $formData = $this->commandDecorator->create($this->getUser(), $request);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/{uuid}/edit', name: 'app_unit_edit_get', methods: ['GET'])]
    public function updateUnitGet(Request $request, Uuid $uuid): Response
    {
        $formData = $this->controllerHelper->getFormData($this->getUser(), $this->queryInteractor, $this->unitHelper, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/{uuid}/edit', name: 'app_unit_edit_post', methods: ['POST'])]
    public function updateUnitPost(#[RequestBody] UnitUpdateCommand $request, Uuid $uuid): Response
    {
        $formData = $this->commandDecorator->update($this->getUser(), $request, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('unit/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/unit/{uuid}/delete', name: 'app_unit_delete')]
    public function delete(Request $request, Uuid $uuid): Response
    {
        $this->commandDecorator->delete($this->getUser(), $uuid);

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
