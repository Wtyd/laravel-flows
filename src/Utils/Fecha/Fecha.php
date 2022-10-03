<?php

declare(strict_types=1);

namespace Src\Utils\Fecha;

use DateTime;
use Src\Utils\Fecha\Exception\FechaNoValidaException;

class Fecha
{
    const DATE_FORMAT = "Y-m-d";

    private ?string $fecha;

    public function __construct(?string $fecha)
    {
        if ($fecha !== null && $this->validarFecha($fecha) === false) {
            throw FechaNoValidaException::paraFormatoIncorrecto($fecha);
        }

        $this->fecha = $fecha;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    private function validarFecha(string $fechaOriginal)
    {
        $fechaComprobar = DateTime::createFromFormat(self::DATE_FORMAT, $fechaOriginal);
        return $fechaComprobar && $fechaComprobar->format(self::DATE_FORMAT) === $fechaOriginal;
    }
}
