<?php

namespace Src\Usuario\Gestion\Infrastructure\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Utils\Foundation\Http\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Src\Usuario\Gestion\Application\ActualizarUsuarioCommand;
use Src\Usuario\Gestion\Application\CrearUsuarioCommand;
use Src\Usuario\Gestion\Application\ListadoUsuariosCommand;
use Src\Usuario\Gestion\Application\ActualizarUsuarioRequestDTO;
use Src\Usuario\Gestion\Application\CrearUsuarioRequestDTO;
use Src\Usuario\Gestion\Application\ObtenerUsuarioPorIdCommand;

/**
 * https://laravel.com/docs/8.x/controllers#resource-controllers
 *
 * Convenciones:
 * - El command que resuelve la ruta es el primer parámetro del método.
 * - Siempre que el usuario envie datos se deberán validar en un FormRequest
 */
class UsuarioController extends Controller
{
    public function index(ListadoUsuariosCommand $listadoUsuariosCommand)
    {
        return $listadoUsuariosCommand->run();
    }


    public function store(CrearUsuarioCommand $crearUsuarioCommand, UsuarioRequest $request)
    {
        $usuario = $this->hydrate(CrearUsuarioRequestDTO::class, $request->validated());
        $usuario->permisos = PermisosTransformer::fromRequest($request->validated());

        $user = $crearUsuarioCommand->run($usuario);

        return ApiResponse::json($user, ApiResponse::ESTADO_200_OK);
    }

    /** Actualizar usuario [all]
     * .
     *
     * @param  UsuarioRequest  $request
     * @param  int  $user
     * @return JsonResponse
     */
    public function update(ActualizarUsuarioCommand $actualizarUsuarioCommand, UsuarioRequest $request, int $user)
    {
        $usuario = $this->hydrate(ActualizarUsuarioRequestDTO::class, $request->validated());
        $usuario->permisos = PermisosTransformer::fromRequest($request->validated());
        $usuario->password = $usuario->password ?? ''; //Hydrator al no tener valor guarda null pero debe ser string ''.

        $user = $actualizarUsuarioCommand->run($user, $usuario);


        return ApiResponse::json($user, ApiResponse::ESTADO_200_OK);
    }

    /**
     * Usuario logueado.
     *
     */
    public function self()
    {
        $user = Auth::user();
        //TODO Este método se esta podrá estandarizar cuando se refactorice el login con Sanctum
        // Además no se puede estandarizar la respuesta si no se refactoriza la parte de Vue que
        // lee los datos.
        // $usuario = $obtenerUsuarioPorIdCommand->run($user->id);

        // return ApiResponse::json($usuario, ApiResponse::ESTADO_200_OK);
        return response()->api($user->withFullData());
    }

    public function show(ObtenerUsuarioPorIdCommand $obtenerUsuarioPorIdCommand, $id)
    {
        $usuario = $obtenerUsuarioPorIdCommand->run($id);

        return ApiResponse::json($usuario, ApiResponse::ESTADO_200_OK);
    }
}
