<?php

declare(strict_types=1);

namespace Src\Utils\Fecha\Exception;

use DomainException;

class FechaNoValidaException extends DomainException
{
    public static function paraFormatoIncorrecto(string $fecha)
    {
        $exception = new self("La fecha $fecha no cumple el formato 'Y-m-d'.");

        return $exception;
    }
}
