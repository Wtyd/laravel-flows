<?php

declare(strict_types=1);

namespace Tests\Utils\CustomFaker;

use Faker\Provider\Base;

class EnergiaProvider extends Base
{
    /**
     * Un cups está formado por:
     * Código país (dos letras): ES, PT, FR
     * Código distribuidora (cuatro dígitos): 0012
     * Número cups (doce dígitos): 456789123456
     * Letras de corrección (dos letras mayúsculas): NB
     *
     * Un cups tiene la forma: ES0073000000970122NB
     *
     * @return string
     */
    public function cups(): string
    {
        return strtoupper($this->bothify('ES################??'));
    }

    /**
     * Código de Distribuidora.
     * Formado por cuatro dígitos.
     *
     * @return string
     */
    public function codigoDistribuidora(): string
    {
        return $this->numerify('####');
    }
}
