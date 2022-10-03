<?php

namespace Src\Usuario\Gestion\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $usuario = $this->faker->persona();

        return [
            'name' => $usuario->nombreCompleto(),
            'email' => $usuario->email(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone_number' => $usuario->telefono(),
            'description' => $this->faker->sentence(),
            'avatar' => '/avatars/default.png',
        ];
    }
}
