<?php

namespace Tests\System\API\Usuario;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

/**
 * Tip: Verificar el status al final ya que si se ha producido un error 500 no te devolverá traza del error.
 */
class CrearUsuarioTest extends TestCase
{
    use WithoutMiddleware;
    use WithFaker;

    /** @test*/
    function status_200_cuando_se_crea_un_nuevo_usuario()
    {
        $user = User::factory()->make();
        $permiso = $this->faker->randomElement($this->getTodosLosPermisos());
        $nuevoUsuarioRequest = [
            'nombre' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'telefono' => $user->phone_number,
            'permisos' => [$permiso => true],
        ];

        $response = $this->post('/api/usuarios', $nuevoUsuarioRequest);

        $nuevoUsuarioResponse = [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'permisos' => [0 => $permiso],
        ];

        // $response->dd(); // dumpea la response y para la ejecución
        // $response->decodeResponseJson()->json() //acceder a los datos de la response
        // assertExactJson fallará ya que la estructura data devuelve el id de base de datos y el password hasheado
        $response->assertJson(
            [
                'status' => 200,
                'errors' => [],
                'data' => $nuevoUsuarioResponse
            ],
            true // Hace que la comparación sea estricta
        )
            ->assertJsonStructure([ // También podemos probar la estructura de datos devuelta
                'status', 'errors', 'data' => [
                    'id', 'name', 'email', 'phone_number', 'description', 'avatar', 'password', 'permisos'
                ]
            ])
            ->assertStatus(200)
            ->assertOk(); // Son equivalentes
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
            'Falta el password del usuario' => [
                [
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'telefono' => $user->phone_number,
                ],
                'password'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider faltaUnCampoOblitatorioDataProvider
     */
    function status_422_cuando_falta_un_dato_obligatorio($nuevoUsuarioRequest, $campoObligatorio)
    {
        $response = $this->post('/api/usuarios', $nuevoUsuarioRequest);

        $response->assertJson([
            'status' => 422,
            'errors' => [['mensaje' => "El campo $campoObligatorio es obligatorio."]],
        ])
            ->assertStatus(422);
    }


    /** @test*/
    function status_422_cuando_falta_mas_de_un_dato_obligatorio()
    {
        $nuevoUsuarioRequest = [
            'permisos' => ['ver_usuario' => true],
        ];
        $response = $this->post('/api/usuarios', $nuevoUsuarioRequest);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'errors' => [
                    ['mensaje' => 'El campo nombre es obligatorio.'],
                    ['mensaje' => 'El campo password es obligatorio.'],
                    ['mensaje' => 'El campo email es obligatorio.'],
                ],
            ]);
    }


    /**
     * @test
     * Los DataProvider se ejecutan en la preparación de los tests cases por lo que NO se debe de hacer inserciones en bdd
     * (factory()->create()) ya que se ejecutan fuera de las transacciones.
     */
    function status_422_cuando_se_intenta_crear_un_usuario_repetido()
    {
        $user1 = User::factory()->create();

        $user2 = User::factory()->make();

        $requestEmailRepetido = $requestTelefonoRepetido = $requestAmbosCamposRepetidos = [
            'nombre' => $user2->name,
            'email' => $user2->email,
            'password' => 'password',
            'telefono' => $user2->phone_number,
            'permisos' => ['ver_usuarios' => true], //no puedo usar faker en el dataProvider
        ];

        $requestEmailRepetido['email'] = $user1->email;

        // Caso 1: Email repetido
        $response = $this->post('/api/usuarios', $requestEmailRepetido);

        $response->assertJson([
            'status' => 422,
            'errors' => [['mensaje' => 'El valor del campo email ya está en uso.']],
        ])
            ->assertStatus(422);

        // Caso 2: Telefono repetido
        $requestTelefonoRepetido['telefono'] = $user1->phone_number;
        $response = $this->post('/api/usuarios', $requestTelefonoRepetido);

        $response->assertJson([
            'status' => 422,
            'errors' => [['mensaje' => 'El valor del campo telefono ya está en uso.']],
        ])
            ->assertStatus(422);

        // Caso 3: Email y Telefono repetidos
        $requestAmbosCamposRepetidos['email'] = $user1->email;
        $requestAmbosCamposRepetidos['telefono'] = $user1->phone_number;
        $response = $this->post('/api/usuarios', $requestAmbosCamposRepetidos);

        $response->assertJson([
            'status' => 422,
            'errors' => [
                ['mensaje' => 'El valor del campo email ya está en uso.'],
                ['mensaje' => 'El valor del campo telefono ya está en uso.']
            ],
        ])
            ->assertStatus(422);
    }
}
