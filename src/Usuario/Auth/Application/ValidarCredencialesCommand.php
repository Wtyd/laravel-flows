<?php

declare(strict_types=1);

namespace Src\Usuario\Auth\Application;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Src\Usuario\Auth\Domain\LoginInterface;
use Src\Usuario\Auth\Domain\Exception\PasswordIncorrectoException;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Zataca\Hydrator\HydratorTrait;

class ValidarCredencialesCommand
{
    use HydratorTrait;

    protected $usuarioRepository;
    protected $loginService;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository, LoginInterface $loginService)
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->loginService = $loginService;
    }

    /**
     * Undocumented function
     *
     * @param ValidarCredencialesDTO $validarCredencialesDTO
     * @return User
     * @throws ModelNotFoundException
     */
    public function run(ValidarCredencialesDTO $validarCredencialesDTO): User
    {
        if ($this->loginService->loginEmail($validarCredencialesDTO->identity, $validarCredencialesDTO->password)) {
            $user = User::where('email', $validarCredencialesDTO->identity)
                ->orWhere('phone_number', $validarCredencialesDTO->identity)->sole();

            $user->withFullData();

            return $user;
        }

        throw PasswordIncorrectoException::porPassword($validarCredencialesDTO->password);
    }
}
