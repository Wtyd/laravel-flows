<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Application;

use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;

class ActualizarUsuarioCommand
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function run(int $id, ActualizarUsuarioRequestDTO $actualizarUsuarioRequestDTO)
    {
        $usuario = new Usuario(
            id: $id,
            name: $actualizarUsuarioRequestDTO->nombre,
            email: $actualizarUsuarioRequestDTO->email,
            phoneNumber: $actualizarUsuarioRequestDTO->telefono,
            password: $actualizarUsuarioRequestDTO->password,
            permisos: $actualizarUsuarioRequestDTO->permisos,
        );

        return $this->usuarioRepository->updateById($id, $usuario);
    }
}
