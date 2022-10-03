<?php

namespace Src\Usuario\Gestion\Infrastructure\Web;

use Filterio\Support\Foundation\Traits\DatagridTrait;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;

class UsuariosDatagrid
{
    use DatagridTrait;

    public $query;
    public $fileName;
    protected $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->query = $this->queryBase();
        $this->fileName = "usuarios.xlsx";
    }

    public function queryBase()
    {
        return $this->usuarioRepository->datagrid(['id', 'name', 'email']);
    }

    /**
     * Headers del excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Correo'
        ];
    }

    //phpcs:disable Squiz.NamingConventions.ValidVariableName
    public function map($user): array
    {
        return [
            $user->{'users.id'},
            $user->{'users.name'},
            $user->{'users.email'}
        ];
    }
}
