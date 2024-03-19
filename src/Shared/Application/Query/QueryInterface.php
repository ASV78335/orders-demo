<?php

namespace App\Shared\Application\Query;

use Symfony\Component\Security\Core\User\UserInterface;

interface QueryInterface
{
    public function getAll(UserInterface $user);

    public function getSelection(UserInterface $user, int $offset, int $count);

    public function getItem(UserInterface $user, string $uuid);

    public function getDetails(UserInterface $user, string $uuid);

}
