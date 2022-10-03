<?php

namespace Src\Usuario\Auth\Domain;

interface LoginInterface
{
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function loginEmail(string $username, string $password, bool $recordar = false): bool;
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function loginTelefono(string $telefono, string $password, bool $recordar = false): bool;
}
