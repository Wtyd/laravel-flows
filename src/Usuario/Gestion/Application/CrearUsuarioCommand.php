<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Application;

use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;
use Src\Utils\Domain\Exception\PersistenceException;

class CrearUsuarioCommand
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param CrearUsuarioRequestDTO $crearUsuarioRequestDTO
     * @return array
     */
    public function run(CrearUsuarioRequestDTO $crearUsuarioRequestDTO): array
    {
        $id = $this->usuarioRepository->nextValId();

        $usuario = new Usuario(
            name: $crearUsuarioRequestDTO->nombre,
            id: $id,
            email: $crearUsuarioRequestDTO->email,
            phoneNumber: $crearUsuarioRequestDTO->telefono,
            password: $crearUsuarioRequestDTO->password,
            permisos: $crearUsuarioRequestDTO->permisos,
        );

        $this->usuarioRepository->create($usuario);

        return $usuario->toArray();
    }
}
