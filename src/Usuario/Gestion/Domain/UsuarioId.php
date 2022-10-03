<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Domain;

class UsuarioId
{
    public function __construct(
        private int $id,
        private string $email,
        private string $phoneNumber
    ) {
        // Validaciones de dominio
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getId()
    {
        return $this->id;
    }

    public function equals(int $id)
    {
        return $this->id === $id;
    }
}
