<?php

namespace App\Controller;

use App\ControllerHelper\CommandDecorator;
use App\ControllerHelper\ControllerHelper;
use App\ControllerHelper\DecoratorFactory;
use App\Product\Application\Command\ProductCommandInteractor;
use App\Product\Application\Command\ProductCreateCommand;
use App\Product\Application\Command\ProductUpdateCommand;
use App\Product\Application\ProductHelper;
use App\Product\Application\Query\ProductQueryInteractor;
use App\Shared\Resources\Attribute\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

class ProductController extends AbstractController
{
    private readonly CommandDecorator  $commandDecorator;

    public function __construct(
        private readonly ControllerHelper           $controllerHelper,
        private readonly ProductHelper              $productHelper,
        private readonly ProductQueryInteractor     $queryInteractor,
        private readonly ProductCommandInteractor   $commandInteractor,
        private readonly DecoratorFactory           $factory
    )
    {
        $this->commandDecorator = $this->factory->createCommandDecorator($this->commandInteractor, $this->queryInteractor, $this->productHelper);
    }


    #[IsGranted('ROLE_USER')]
    #[Route('/products/{page}', name: 'app_products')]
    public function getSelection(Request $request, int $page): Response
    {
        $pagination = $this->controllerHelper->getPagination($this->productHelper, (int)$request->cookies->get('entitiesForPage'), $page);
        $products = $this->queryInteractor->getSelection($this->getUser(), (int)$pagination['offset'], $request->cookies->get('entitiesForPage'))->getItems();
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/index.html.twig', array_merge($environment, $pagination, ['products' => $products]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/{uuid}/view', name: 'app_product_view')]
    public function getDetails(Request $request, Uuid $uuid): Response
    {
        $product = $this->queryInteractor->getDetails($this->getUser(), $uuid->toRfc4122());
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/view.html.twig', array_merge($environment, ['product' => $product]));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/create', name: 'app_product_create_get', methods: ['GET'])]
    public function createProductGet(Request $request): Response
    {
        $formData = $this->controllerHelper->getNewFormData($this->getUser(), $this->productHelper);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/create', name: 'app_product_create_post', methods: ['POST'])]
    public function createProductPost(#[RequestBody] ProductCreateCommand $request): Response
    {
        $formData = $this->commandDecorator->create($this->getUser(),$request);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/create.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/{uuid}/edit', name: 'app_product_edit_get', methods: ['GET'])]
    public function updateProductGet(Request $request, Uuid $uuid): Response
    {
        $formData = $this->controllerHelper->getFormData($this->getUser(), $this->queryInteractor, $this->productHelper, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/{uuid}/edit', name: 'app_product_edit_post', methods: ['POST'])]
    public function updateProductPost(#[RequestBody] ProductUpdateCommand $request, Uuid $uuid): Response
    {
        $formData = $this->commandDecorator->update($this->getUser(), $request, $uuid);
        $environment = $this->controllerHelper->getEnvironment($request);

        return $this->render('product/edit.html.twig', array_merge($formData, $environment));
    }


    #[IsGranted('ROLE_USER')]
    #[Route(path: '/product/{uuid}/delete', name: 'app_product_delete')]
    public function delete(Request $request, Uuid $uuid): Response
    {
        $this->commandDecorator->delete($this->getUser(), $uuid);

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
