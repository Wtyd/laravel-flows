<?php

namespace Tests\System\API\Usuario;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

/**
 * Tip: Verificar el status al final ya que si se ha producido un error 500 no te devolverá traza del error.
 */
class ListadoUsuariosTest extends TestCase
{
    use WithoutMiddleware;

    /** @test*/
    function status_200_cuando_se_listan_todos_los_usuarios()
    {
        User::factory(10)->create();

        $response = $this->get('/api/usuarios');


        $expectedUsers = User::select('id as users.id', 'name as users.name', 'email as users.email')->get()->toArray();
        $response->assertExactJson($expectedUsers) // Filterio se encarga de la respuesta que no está normalizada
            ->assertJsonStructure([['users.id', 'users.name', 'users.email']]) // También podemos probar la estructura de datos devuelta
            ->assertStatus(200);

        $this->assertCount(11, json_decode($response->getContent())); // 10 + el admin
    }

    /** @test*/
    function status_200_y_array_vacio_cuando_no_hay_usuarios_en_el_sistema()
    {
        $admin = User::find(1);

        $admin->delete();
        $response = $this->get('/api/usuarios');

        $expectedUsers = User::select('id as users.id', 'name as users.name', 'email as users.email')->get()->toArray();
        $response->assertExactJson($expectedUsers) // Filterio se encarga de la respuesta que no está normalizada

            ->assertJsonStructure([])
             ->assertStatus(200);

        $this->assertCount(0, json_decode($response->getContent()));
    }
}
