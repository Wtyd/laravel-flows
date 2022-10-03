<?php

namespace Tests\System\API\Usuario;

use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Tests\TestCase;

class SanctumTokenTest extends TestCase
{
    function devuelve_200_cuando_un_usuario_crea_un_token_sanctum()
    {
        $this->markTestSkipped("Implementar create token si es necesario");
        $user = User::factory()->create();

        $loginRequest = [
            'identity' => $user->phone_number,
            'password' => 'password',
            'device_name' => $user->name
        ];

        $response = $this->post(route('token.create'), $loginRequest); // Usando el nombre de la ruta
         // $response = $this->post('api/sanctum/tokens/create', $loginRequest); // Modo normal

        $response->assertJson(
            [
                'data' => [
                    'expiration' => 'nunca'
                ],
                'status' => 200,
                'errors' => []
            ]
        );
        $response->assertJsonStructure([
            'data' => [
                'token',
            ]
        ]);

        $token = User::with('tokens')->find($user->id)->tokens()->first();

        $responseToken = $response->json()['data']['token'];

        $this->assertEquals($token->name, $user->name);
        $this->assertEquals(PersonalAccessToken::findToken($responseToken), $token);
        $this->assertEquals($token->abilities, ['*']);

        // Sanctum::actingAs($user, ['*']);

        // $response = $this->get('/api/usuarios');

        // $response = $this->withHeaders([
        //     'Authorization' => 'Bearer '.$responseToken,
        //     'Accept' => 'application/json'
        // ])->get('api/user');
        // $response->dd();
    }
    //3|pF2KoJtANVsumvuc5YfpU9gvOUTtaRGWCMwGv6Vw


    function un_usuario_puede_acceder_a_las_rutas_bajo_autentificacion_sanctum_con_api_token()
    {
        $this->markTestSkipped("Implementar si es necesario");
        User::factory(3)->create();

        Sanctum::actingAs(User::find(1), ['*']);

        $response = $this->get('/api/usuarios');

        $this->assertCount(4, $response->json());
    }


    function un_usuario_puede_acceder_a_las_rutas_bajo_autentificacion_sanctum_mediante_login_web()
    {
        $this->markTestSkipped("Implementar si es necesario");
        User::factory(3)->create();

        $response = $this->actingAs(User::find(1))->get('/api/usuarios');

        $this->assertCount(4, $response->json());
    }


    function devuelve_la_lista_de_tokens_del_usuario()
    {
        $this->markTestSkipped("Implementar si es necesario");
        $user = User::factory()->create();

        $token1 = $user->createToken('token 1');
        $token2 = $user->createToken('token 2');

        // dd($token1->accessToken->toArray());
        Sanctum::actingAs($user, ['*']);

        $response = $this->get(route('token.index'));
        // $response = $this->get('/api/sanctum/tokens');

        $response->assertOk()
        ->assertJson([
            'data' => [
                $token2->accessToken->toArray(),
                $token1->accessToken->toArray(),
            ]
        ]);
    }


    function devuelve_200_cuando_un_usuario_elimina_un_token()
    {
        $this->markTestSkipped("Implementar si es necesario");
        $user = User::factory()->create();

        $user->createToken('token 1');

        Sanctum::actingAs($user, ['*']);

        $response = $this->delete(route('token.delete', 'token 1'));

        $response->assertOk()
        ->assertJson([
            'data' => 'El token ha sido eliminado con éxito.',
            'status' => 200
        ]);
    }


    function devuelve_404_cuando_un_usuario_intenta_eliminar_un_token_que_no_existe()
    {
        $this->markTestSkipped("Implementar si es necesario");
        $user = User::factory()->create();

        $user->createToken('token 1');

        Sanctum::actingAs($user, ['*']);

        $response = $this->delete(route('token.delete', 'token inventado'));

        $response->assertNotFound()
        ->assertJson([
            'errors' => [['mensaje' => 'El recurso no ha sido encontrado']],
            'status' => 404
        ]);
    }


    function devuelve_404_cuando_un_usuario_intenta_eliminar_el_token_de_otro_usuario()
    {
        $this->markTestSkipped("Implementar si es necesario");
        $user = User::factory()->create();

        $user->createToken('token 1');

        $user2 = User::factory()->create();

        $user2->createToken('token usuario 2');

        Sanctum::actingAs($user, ['*']);

        $response = $this->delete(route('token.delete', 'token usuario 2'));

        $response->assertNotFound()
        ->assertJson([
            'errors' => [['mensaje' => 'El recurso no ha sido encontrado']],
            'status' => 404
        ]);
    }
}
