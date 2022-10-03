<?php

namespace Tests\Unit;

use Mockery\MockInterface;
use Src\Usuario\Gestion\Application\CrearUsuarioCommand;
use Src\Usuario\Gestion\Application\CrearUsuarioRequestDTO;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    /** @test*/
    public function crea_un_nuevo_usuario_en_el_sistema()
    {
        //TODO probar los commands que como no tienen lógica puedo probar las excepciones al crear usuarios.
        $mock = $this->mock(UsuarioRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('nextValId')->andReturn(99);
            $mock->shouldReceive('create')->once();
        });

        $command = $this->app->make(CrearUsuarioCommand::class);

        $user = User::factory()->make();
        $request = new CrearUsuarioRequestDTO(
            nombre: $user->name,
            email: $user->email,
            password: 'password',
            telefono: $user->phone_number,
            permisos: [],
        );

        $command->run($request);
    }
}
