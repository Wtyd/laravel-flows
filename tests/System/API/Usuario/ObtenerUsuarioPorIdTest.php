<?php

namespace Tests\System\API\Usuario;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

/**
 * Tip: Verificar el status al final ya que si se ha producido un error 500 no te devolverá traza del error.
 */
class ObtenerUsuarioPorIdTest extends TestCase
{
    // use WithoutMiddleware;
    use WithFaker;

    /** @test*/
    function status_200_y_datos_de_usuario_por_id()
    {
        //TODO
        // 1 Añadidos Middlewares en Http/Kernel sin los cuales la forma actual de usar los permisos no funciona
        // Esto es, con el middleware por defecto de laravel se peude hacer can:permiso
        // Estos Middlewares añaden permission, role y role_or_permission
        // 2 El superAdmin tiene un unico permiso manage_users que no está relacionado con ver_usuarios o editar_usuarios
        // Creo que el rol debería tener todos los permisos relacionados con los usuarios.
        $users = User::factory(10)->create();

        $user = $users->first();
        // dd($this->getTodosLosPermisos());
        $permiso = $this->faker->randomElement($this->getTodosLosPermisos());
        $user->givePermissionTo($permiso);

        $response = $this->actingAs(User::find(1))->get('/api/usuarios/' . $user->id);
        // $response = $this->get('/api/usuarios/' . $user->id);

        $usuarioEsperado = Usuario::create($user->toArray());
        $usuarioEsperado = $usuarioEsperado->toArray();
        unset($usuarioEsperado['password']);
        $response->assertJson([
            'status' => 200,
            'errors' => [],
            'data' => $usuarioEsperado
        ])
            ->assertStatus(200);
    }

    /** @test*/
    function status_404_y_mensaje_de_error_cuando_no_encuentra_el_usuario_por_id()
    {
        $users = User::factory(10)->create();

        $user = $users->first();
        $user->delete();

        $admin = User::find(1);
        $response = $this->actingAs($admin)->get('/api/usuarios/' . $user->id);

        $response->assertJson([
            'status' => 404,
            'errors' => [['mensaje' => 'El recurso no ha sido encontrado']],
            'data' => []
        ])
             ->assertStatus(404);
    }
}
