<?php

namespace App\ControllerHelper;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Command\DTOCreateInterface;
use App\Shared\Application\Command\DTOUpdateInterface;
use App\Shared\Application\EntityHelperInterface;
use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommandDecorator implements CommandInterface
{

    public function __construct(
        private readonly CommandInterface $commandInteractor,
        private readonly QueryInterface $queryInteractor,
        private readonly EntityHelperInterface $entityHelper
    )
    {
    }

    use DecoratorTrait {
        DecoratorTrait::__construct as public __traitConstruct;
    }

    public function create(UserInterface            $user,
                           DTOCreateInterface      $request
    ): array
    {
        $result = $this->commandInteractor->create($user, $request);

        if (null !== $result) $this->requestStack->getSession()->getFlashBag()->add('success', 'Done');

        $entity = $this->entityHelper->getNewDetails();
        $options = $this->entityHelper->getRequestOptions($user);

        $entityForm = $this->createForm($this->entityHelper->getInstanceName(), $entity, $options);

        return compact('entity', 'entityForm');
    }

    public function update(UserInterface            $user,
                           DTOUpdateInterface       $request,
                           string                   $uuid
    ): array
    {
        $result = $this->commandInteractor->update($user, $request, $uuid);

        if (null !== $result) $this->requestStack->getSession()->getFlashBag()->add('success', 'Done');

        $entity = $this->queryInteractor->getDetails($user, $uuid);
        $options = $this->entityHelper->getRequestOptions($user);

        $entityForm = $this->createForm($this->entityHelper->getInstanceName(), $entity, $options);

        return compact('entity', 'entityForm');
    }

    public function delete(UserInterface    $user,
                           string             $uuid)
    : void
    {
        $this->commandInteractor->delete($user, $uuid);

        $this->session->getFlashBag()->add('success', 'Done');
    }
}
