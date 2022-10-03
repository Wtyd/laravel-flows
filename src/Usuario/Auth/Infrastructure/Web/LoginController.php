<?php

namespace Src\Usuario\Auth\Infrastructure\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Usuario\Auth\Application\ValidarCredencialesCommand;
use Src\Usuario\Auth\Application\ValidarCredencialesDTO;
use Src\Usuario\Auth\Infrastructure\Web\AuthenticateRequest;
use Src\Utils\Foundation\Http\ApiResponse;

class LoginController extends Controller
{
    public function __construct(protected ValidarCredencialesCommand $validarCredencialesCommand)
    {
    }

    public function login(AuthenticateRequest $request)
    {
        $validarCredencialesDTO = $this->hydrate(ValidarCredencialesDTO::class, $request->validated());

        $user = $this->validarCredencialesCommand->run($validarCredencialesDTO);

        return ApiResponse::json($user->toArray(), ApiResponse::ESTADO_200_OK);
    }

    /**
     * Usuario logueado.
     *
     */
    public function self()
    {
        $user = Auth::user();
        $user->withFullData();

        return response()->api($user);
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response([], 200);
    }
}
