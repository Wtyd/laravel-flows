<?php

namespace Tests\Integration\DataSource\Usuario;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Src\Usuario\Gestion\Infrastructure\Persistence\UsuarioRepository;
use Tests\TestCase;

class UsuarioRepositoryTest extends TestCase
{
    use WithFaker;

    public function permisosDataProvider()
    {
        return [
            'Sin permisos' => [[]],
            'Con un único permiso' => [['permiso_inventado']],
            'Con múltiples permisos' => [['permiso inventado', 'otro_permiso_inventado']]
        ];
    }
    /**
     * @test
     * @dataProvider permisosDataProvider
     */
    function persiste_un_usuario_en_la_bdd($permisos)
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $user = User::factory()->make();

        $this->crearPermisos($permisos);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: $permisos,
            password: 'password'
        );

        $resultado = $repository->create($usuario);

        $this->assertTrue($resultado);

        $usuarioEsperado = $usuario->toArray();
        unset($usuarioEsperado['permisos']);

        $this->assertDatabaseHas('users', $usuarioEsperado);
        $user->hasAllPermissions($permisos);

        $permisos = $usuario->getPermisos();
    }

    /** @test*/
    function el_campo_name_admite_hasta_255_caracteres_como_máximo()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $name = $this->faker->regexify('[A-Za-z]{255}');

        $user = User::factory()->make(['name' => $name]);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: [],
            password: 'password'
        );

        $resultado = $repository->create($usuario);

        $this->assertTrue($resultado);

        $this->assertDatabaseHas('users', ['name' => $usuario->getName()]);
    }

    /** @test*/
    function lanza_excepcion_cuando_name_supera_los_255_caracteres()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $name = $this->faker->regexify('[A-Za-z]{256}');

        $user = User::factory()->make(['name' => $name]);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: [],
            password: 'password'
        );

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[22001]: String data, right truncated: 7 ERROR:  el valor es demasiado largo para el tipo character varying(255)');
        $repository->create($usuario);
    }

    /** @test*/
    function el_campo_name_admite_caracteres_especiales()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $name = 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ';
        $user = User::factory()->make(['name' => $name]);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: [],
            password: 'password'
        );

        $resultado = $repository->create($usuario);

        $this->assertTrue($resultado);

        $this->assertDatabaseHas('users', ['name' => $usuario->getName()]);
    }

    /** @test*/
    function el_campo_phone_number_admite_caracteres_no_numericos()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $telefono = '+34 999 999 999';
        $user = User::factory()->make(['phone_number' => $telefono]);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: [],
            password: 'password'
        );

        $resultado = $repository->create($usuario);

        $this->assertTrue($resultado);

        $this->assertDatabaseHas('users', ['phone_number' => $usuario->getPhoneNumber()]);
    }
}
