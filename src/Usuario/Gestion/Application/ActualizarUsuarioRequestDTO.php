<?php

namespace Src\Usuario\Gestion\Application;

class ActualizarUsuarioRequestDTO //extends UsuarioBaseDTO
{
    public function __construct(
        public string $nombre,
        public string $email,
        public ?string $password,
        public string $telefono,
        public array $permisos
    ) {
    }
}
