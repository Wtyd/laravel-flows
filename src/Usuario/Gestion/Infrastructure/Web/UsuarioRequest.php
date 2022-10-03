<?php

namespace Src\Usuario\Gestion\Infrastructure\Web;

use Src\Utils\Foundation\Http\APIRequest;

class UsuarioRequest extends APIRequest
{
    protected function store()
    {
        return [
            'password' => 'required|min:6',
            'email' => 'required|email|max:255|unique:users',
            'telefono' => 'min:9|unique:users,phone_number',
        ];
    }

    protected function update()
    {
        return [
            'password' => 'min:6',
            'email'    => 'required', 'unique:users' . $this->user,
            'telefono' => 'required|min:9', 'unique:phone_number' . $this->user,
        ];
    }

    public function rules()
    {
        return [
            'nombre' => 'required|max:255',
            'permisos' => 'array',
        ]
        +
        ($this->isMethod('POST') ? $this->store() : $this->update());
    }
}
