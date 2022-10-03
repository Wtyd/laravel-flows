<?php

namespace Src\Usuario\Auth\Infrastructure;

use Illuminate\Support\Facades\Auth;
use Src\Usuario\Auth\Domain\LoginInterface;

class LoginService implements LoginInterface
{
    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function loginEmail(string $username, string $password, bool $recordar = false): bool
    {
        if (Auth::attempt(['email' => $username, 'password' => $password], $recordar)) {
            return true;
        }

        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function loginTelefono(string $telefono, string $password, bool $recordar = false): bool
    {
        if (Auth::attempt(['email' => $telefono, 'password' => $password], $recordar)) {
            return true;
        }

        return false;
    }
}
