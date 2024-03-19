<?php

namespace App\Controller;

use App\Category\Application\CategoryHelper;
use App\Category\Application\Command\CategoryCommandInteractor;
use App\Category\Application\Command\CategoryCreateCommand;
use App\Category\Application\Command\CategoryUpdateCommand;
use App\Category\Application\Query\CategoryQueryInteractor;
use App\ControllerHelper\CommandDecorator;
use App\ControllerHelper\ControllerHelper;
use App\ControllerHelper\DecoratorFactory;
use App\Shared\Resources\Attribute\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class CategoryController extends AbstractController
{
    private readonly CommandDecorator  $commandDecorator;

    public function __construct(
        private readonly ControllerHelper           $controllerHelper,
        private readonly CategoryHelper             $categoryHelper,
        private readonly CategoryQueryInteractor    $queryInteractor,
        private readonly CategoryCommandInteractor  $commandInteractor,
        private readonly DecoratorFactory           $factory
        )
    {
        $this->commandDecorator = $this->factory->createCommandDecorator($this->commandInteractor, $this->queryInteractor, $this->categoryHelper);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/categories/{page}', name: 'app_categories')]
    public function getSelection(Request $request, int $page): Response
    {
        $pagination = $this->controllerHelper->getPagination($this->categoryHelper, (int)$request->cookies->get('entitiesForPage'), $page);
        $categories = $this->queryInteractor->getSelection($this->getUser(), (int)$pagination['offset'], $request->cookies->get('entitiesForPage'))->getItems();
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/index.html.twig', array_merge($environment, $pagination, ['categories' => $categories]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/{uuid}/view', name: 'app_category_view')]
    public function getDetails(Request $request, Uuid $uuid): Response
    {
        $category = $this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122());
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/view.html.twig', array_merge($environment, ['category' => $category]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/create', name: 'app_category_create_get', methods: ['GET'])]
    public function createCategoryGet(Request $request): Response
    {
        $formData = $this->controllerHelper->getNewFormData($this->getUser(), $this->categoryHelper);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/create', name: 'app_category_create_post', methods: ['POST'])]
    public function createCategoryPost(#[RequestBody] CategoryCreateCommand $request): Response
    {
        $formData = $this->commandDecorator->create($this->getUser(), $request);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/{uuid}/edit', name: 'app_category_edit_get', methods: ['GET'])]
    public function updateCategoryGet(Request $request, Uuid $uuid): Response
    {
        $formData = $this->controllerHelper->getFormData($this->getUser(), $this->queryInteractor, $this->categoryHelper, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/{uuid}/edit', name: 'app_category_edit_post', methods: ['POST'])]
    public function updateCategoryPost(#[RequestBody] CategoryUpdateCommand $request, Uuid $uuid): Response
    {
        $formData = $this->commandDecorator->update($this->getUser(), $request, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('category/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/category/{uuid}/delete', name: 'app_category_delete')]
    public function delete(Request $request, Uuid $uuid): Response
    {
        $this->commandDecorator->delete($this->getUser(), $uuid);

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
