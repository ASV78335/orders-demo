<?php

namespace App\ControllerHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait DecoratorTrait
{
    private SessionInterface $session;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack
    )
    {
        $this->session = $this->requestStack->getSession();
    }
    protected function createForm(string $name, $entity, array $options): FormInterface
    {
        $path = sprintf("App\Form\%sType", $name);
        return $this->formFactory->create($path, $entity, $options);
    }
}
