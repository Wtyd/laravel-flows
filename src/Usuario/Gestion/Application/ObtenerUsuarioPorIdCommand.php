<?php

namespace Src\Usuario\Gestion\Application;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;

class ObtenerUsuarioPorIdCommand
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Devuelve los datos de un usuario
     *
     * @param integer $id
     * @return array Los datos del usuario.
     *
     * @throws ModelNotFoundException
     */
    public function run(int $id)
    {
        return $this->usuarioRepository->findById($id)->toArray();
    }
}
