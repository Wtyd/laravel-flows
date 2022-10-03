<?php

namespace Tests\System\API\Usuario;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

class ActualizarUsuarioTest extends TestCase
{
    use WithoutMiddleware;
    use WithFaker;

    /** @test*/
    function status_200_cuando_se_actualiza_un_usuario_ya_existente()
    {
        $user = User::factory()->create();

        $datosNuevos = $this->faker->persona();
        $permiso = $this->faker->randomElement($this->getTodosLosPermisos());

        $actualizarUsuarioRequest = [
            'nombre' => $datosNuevos->nombreCompleto(),
            'email' => $datosNuevos->email(),
            'telefono' => $datosNuevos->telefono(),
            'permisos' => [ $permiso => true],
        ];

        $response = $this->put('/api/usuarios/' . $user->id, $actualizarUsuarioRequest);

        $nuevoUsuarioResponse = [
            'name' => $datosNuevos->nombreCompleto(),
            'email' => $datosNuevos->email(),
            'phone_number' => $datosNuevos->telefono(),
            'permisos' => [$permiso],
        ];

        $response->assertJson([
            'status' => 200,
            'errors' => [],
            'data' => $nuevoUsuarioResponse
        ])
            ->assertStatus(200);

        $permisos = $nuevoUsuarioResponse['permisos'];
        unset($nuevoUsuarioResponse['permisos']);
        $this->assertDatabaseHas('users', $nuevoUsuarioResponse);

        $this->assertCount(1, $user->getDirectPermissions()->toArray());
        $this->assertEquals($permisos[0], $user->getDirectPermissions()->toArray()[0]['name']);
    }

    /** @test*/
    function status_404_cuando_se_actualiza_un_usuario_que_NO_existe()
    {
        $datosNuevos = $this->faker->persona();
        $permiso = $this->faker->randomElement($this->getTodosLosPermisos());

        $actualizarUsuarioRequest = [
            'nombre' => $datosNuevos->nombreCompleto(),
            'email' => $datosNuevos->email(),
            'telefono' => $datosNuevos->telefono(),
            'permisos' => [$permiso => true],
        ];

        $response = $this->put('/api/usuarios/' . -1, $actualizarUsuarioRequest);

        $response->assertJson([
            'status' => 404,
            'errors' => [['mensaje' => 'El recurso no ha sido encontrado']],
            'data' => null
        ])
            ->assertStatus(404);
    }

    public function faltaUnCampoOblitatorioDataProvider()
    {
        $user = User::factory()->make();
        return [
            'Falta el nombre del usuario' => [
                [
                    'email' => $user->email,
                    'password' => 'password',
                    'telefono' => $user->phone_number,
                ],
                'nombre'
            ],
            'Falta el email del usuario' => [
                [
                    'nombre' => $user->name,
                    'password' => 'password',
                    'telefono' => $user->phone_number,
                ],
                'email'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider faltaUnCampoOblitatorioDataProvider
     */
    function status_422_cuando_se_actualiza_un_usuario_y_falta_un_dato_obligatorio($actualizarUsuarioRequest, $campoObligatorio)
    {
        $user = User::factory()->create();
        $response = $this->put('/api/usuarios/' . $user->id, $actualizarUsuarioRequest);

        $response->assertJson([
            'status' => 422,
            'errors' => [['mensaje' => "El campo $campoObligatorio es obligatorio."]],
        ])
            ->assertStatus(422);
    }

    /** @test*/
    function status_422_cuando_se_actualiza_un_usuario_y_falta_mas_de_un_dato_obligatorio()
    {
        $actualizarUsuarioRequest = [
            'permisos' => ['ver_usuario' => true],
        ];
        $user = User::factory()->create();
        $response = $this->put('/api/usuarios/' . $user->id, $actualizarUsuarioRequest);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'errors' => [
                    ['mensaje' => 'El campo nombre es obligatorio.'],
                    ['mensaje' => 'El campo email es obligatorio.'],
                    ['mensaje' => 'El campo telefono es obligatorio.'],
                ],
            ]);
    }
}
