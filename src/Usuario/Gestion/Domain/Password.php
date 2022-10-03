<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Domain;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Src\Usuario\Gestion\Domain\Exception\PasswordNoValidoException;

class Password
{
    public function __construct(protected ?string $value)
    {
        $this->setValue($value);
    }

    public function isEqualsToString(string $string): bool
    {
        return Hash::check($string, $this->value);
    }

    /**
     * Primero se comprueba la longitud de la cadena. Luego:
     * Si se cambia el password
     *
     * @param string $newValue En texto plano
     * @return void
     */
    protected function setValue(string $newValue): void
    {
        if (!empty($newValue) && !(strlen($newValue) >= 6)) {
            throw PasswordNoValidoException::porPassword($newValue);
        }

        if ($this->isHashed($newValue)) {
            $this->value = $newValue;
        } else {
            $this->value = Hash::make($newValue);
        }
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Determina si una cadena está hasheada o por el contrario es texto plano.
     *
     * El Hash con bcrypt tiene dos carácterísticas:
     * 1. Exactamente 60 carácteres.
     * 2. Los primeros carácteres siempre son '$2y$'
     * @param string $string
     * @return boolean
     */
    protected function isHashed(string $string): bool
    {
        return Str::length($string) === 60 && Str::contains($string, '$2y$');
    }
}
