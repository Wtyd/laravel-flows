<?php

namespace Src\Usuario\Auth\Infrastructure\Web;

use App\Http\Controllers\Controller;
use Src\Usuario\Auth\Application\ValidarCredencialesCommand;
use Src\Usuario\Auth\Application\ValidarCredencialesDTO;
use Src\Usuario\Auth\Infrastructure\Web\CreateTokenRequest;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Src\Utils\Foundation\Http\ApiResponse;

class CreateTokenController extends Controller
{
    public function __construct(protected ValidarCredencialesCommand $validarCredencialesCommand)
    {
    }

    public function __invoke(CreateTokenRequest $request)
    {
        $validarCredencialesDTO = $this->hydrate(ValidarCredencialesDTO::class, $request->validated());

        $user = $this->validarCredencialesCommand->run($validarCredencialesDTO);
        $userModel = User::findOrFail($user->id);

        $data = [
            'token' => $userModel->createToken($request->device_name)->plainTextToken,
            'expiration' => config('sanctum.expiration') ?? 'nunca',
        ];

        return ApiResponse::json($data, ApiResponse::ESTADO_200_OK);
    }
}
