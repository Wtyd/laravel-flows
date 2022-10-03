<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Domain\Exception;

use DomainException;

class PasswordNoValidoException extends DomainException implements UsuarioExceptionInterface
{
    private $password;

    public static function porPassword(string $password)
    {
        $exception = new self('La longitud mínima para el password es es de 6 carácteres.', 400);

        $exception->password = $password;

        return $exception;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
