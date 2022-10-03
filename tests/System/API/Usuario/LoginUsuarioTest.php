<?php

namespace Tests\System\API\Usuario;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;
use Zataca\Hydrator\HydratorTrait;

/**
 * Tip: Verificar el status al final ya que si se ha producido un error 500 no te devolverá traza del error.
 * Se crea el grupo login ya que en la pipeline no se pueden resolver las peticiones Http.
 * @group login
 */
class LoginUsuarioTest extends TestCase
{
    use WithFaker;
    use HydratorTrait;

    /** @test*/
    function login_de_usuario()
    {
        $user = User::find(1)->withFullData();

        $loginRequest = [
            'identity' => $user->email, // email o phone_number
            'password' => 'password',
        ];

        $response = $this->post('api/login', $loginRequest);
        $response->assertJson([
            'data' => $user->toArray()
        ]);

        $response->assertStatus(200);
    }

    /** @test*/
    public function login_incorrecto()
    {
        $user = User::find(1);

        $loginRequest = [
            'identity' => $user->email, // email o phone_number
            'password' => 'incorrecto',
        ];


        // dd("$baseUrl/api/login");

        // $response = $this->post("$baseUrl/api/login", $loginRequest);
        $response = $this->post('api/login', $loginRequest);
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [0 => ['mensaje' => 'El password no se corresponde con el del usuario.']]
        ]);
    }


     /** @test*/
    public function logout()
    {
        $response1 = $this->post('api/logout');

        $response1->assertStatus(302); // Redirección al login

        $user = User::find(1);
        $loginRequest = [
            'identity' => $user->email, // email o phone_number
            'password' => 'password',
        ];
        $baseUrl = config('app.url');
        $response = Http::post("$baseUrl/api/login", $loginRequest);

        $this->assertEquals(200, $response->status());

        $response = Http::post("$baseUrl/api/logout");

        $this->assertEquals(200, $response->status());
        $this->assertEmpty($response->json());
    }
}
