<?php

namespace Src\Usuario\Gestion\Application;

use Filterio\Support\Facades\Filterio;
use Src\Usuario\Gestion\Infrastructure\Web\UsuariosDatagrid;

class ListadoUsuariosCommand
{
    protected $usuariosDatagrid;

    public function __construct(UsuariosDatagrid $usuariosDatagrid)
    {
        $this->usuariosDatagrid = $usuariosDatagrid;
    }
    public function run()
    {
        return Filterio::filter($this->usuariosDatagrid);
    }
}
