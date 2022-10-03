<?php

namespace Src\Usuario\Gestion\Application;

class CrearUsuarioRequestDTO
{
    public function __construct(
        public string $nombre,
        public string $email,
        public string $password,
        public string $telefono,
        public array $permisos
    ) {
    }
}
