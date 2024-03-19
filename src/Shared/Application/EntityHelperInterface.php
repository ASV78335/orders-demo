<?php

namespace App\Shared\Application;

use Symfony\Component\Security\Core\User\UserInterface;

interface EntityHelperInterface
{
    public function getInstanceName(): string;

    public function getNewDetails();

    public function getCountOfNotDeletedEntities(): int;

    public function getRequestOptions(UserInterface $user): array;
}
