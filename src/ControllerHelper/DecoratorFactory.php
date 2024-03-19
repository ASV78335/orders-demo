<?php

namespace App\ControllerHelper;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\EntityHelperInterface;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DecoratorFactory
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack
    )
    {
    }

    public function createCommandDecorator(CommandInterface $commandInteractor,
                                           QueryInterface $queryInteractor,
                                           EntityHelperInterface $entityHelper
    ): CommandDecorator
    {
        $decorator = new CommandDecorator($commandInteractor, $queryInteractor, $entityHelper);
        $decorator->__traitConstruct(
            $this->formFactory,
            $this->requestStack
        );

        return $decorator;
    }
}
