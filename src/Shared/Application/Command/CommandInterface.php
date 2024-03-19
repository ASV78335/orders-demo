<?php

namespace App\Shared\Application\Command;

use Symfony\Component\Security\Core\User\UserInterface;

interface CommandInterface
{

    public function create(UserInterface $user, DTOCreateInterface $request);

    public function update(UserInterface $user, DTOUpdateInterface $request, string $uuid);

    public function delete(UserInterface $user, string $uuid): void;

}
