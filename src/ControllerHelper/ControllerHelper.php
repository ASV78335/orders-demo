<?php

namespace App\ControllerHelper;

use App\Entity\Contragent;
use App\Entity\Person;
use App\Exception\Person\PersonAccessDeniedException;
use App\Service\IndexService;
use App\Service\PersonService;
use App\Shared\Application\EntityHelperInterface;
use App\Shared\Application\PaginationTrait;
use App\Shared\Application\Query\QueryInterface;
use Error;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class ControllerHelper
{
    private SessionInterface $session;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly IndexService $indexService,
        private readonly PersonService $personService,
        private readonly RequestStack $requestStack,
        private readonly Security $security
    )
    {
        $this->session = $this->requestStack->getSession();
    }

    use PaginationTrait;

    public function getNewFormData(UserInterface            $user,
                                   EntityHelperInterface    $entityHelper
    ): array
    {
        $entity = $entityHelper->getNewDetails();
        $options = $entityHelper->getRequestOptions($user);

        $entityForm = $this->createForm($entityHelper->getInstanceName(), $entity, $options);

        return compact('entity', 'entityForm');
    }

    public function getFormData(UserInterface           $user,
                                QueryInterface          $queryInteractor,
                                EntityHelperInterface   $entityHelper,
                                Uuid                    $uuid
    ): array
    {
        $entity = $queryInteractor->getDetails($user, $uuid);
        $options = $entityHelper->getRequestOptions($user);

        $entityForm = $this->createForm($entityHelper->getInstanceName(), $entity, $options);

        return compact('entity', 'entityForm');
    }

    public function getEnvironment($request): array
    {
        $personForm = $this->getPersonForm($request)->createView();
        $data = $this->indexService->getData();

        return compact('data', 'personForm');
    }

    public function getPagination(EntityHelperInterface $entityHelper, int $count, int $page): array
    {
        $countOfNotDeletedEntities = $entityHelper->getCountOfNotDeletedEntities();
        return $this->getPaginationInfo($countOfNotDeletedEntities, $count, $page);
    }

    private function createForm(string $name, $entity, array $options): FormInterface
    {
        $path = sprintf("App\Form\%sType", $name);
        return $this->formFactory->create($path, $entity, $options);
    }


    private function getPersonForm($request): FormInterface
    {
        $person = $this->security->getUser();
        if (!$person instanceof Person) throw new PersonAccessDeniedException();

        $contragents = $this->indexService->getContragents();
        sort($contragents);

        $personForm = $this->createForm('CurrentContragent', $person, [
            'contragents' => $contragents
        ]);

        $formHandleResult = $this->handlePersonForm($personForm, $request, $person->getCurrentContragent());

        if ($formHandleResult === 'Done') {
            $this->session->getFlashBag()->add('success', 'Done');

        } elseif ($formHandleResult === 'Error') {
            $this->session->getFlashBag()->add('error', 'Error!');

        }
        return $personForm;
    }

    private function handlePersonForm(FormInterface $personForm, $request, ?Contragent $oldContragent): string
    {
        If ((!method_exists($request, 'isMethod')) ||
            (!$request->isMethod('POST')) ||
            (!isset($request->request->all()['current_contragent'])))
            return '';

        $personForm->handleRequest($request);
        if ($personForm->isSubmitted() && $personForm->isValid()) {

            $person = $personForm->getData();
            try {
                $this->personService->setCurrentContragent($person, $person->getCurrentContragent());

                return 'Done';

            } catch (Error) {
                $person->setCurrentContragent($oldContragent);
            }
        }
        return 'Error';
    }
}
