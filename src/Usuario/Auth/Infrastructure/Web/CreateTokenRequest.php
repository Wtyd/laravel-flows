<?php

declare(strict_types=1);

namespace Src\Usuario\Auth\Infrastructure\Web;

use Src\Utils\Foundation\Http\APIRequest;

class CreateTokenRequest extends APIRequest
{
    public function rules()
    {
        return [
            'identity' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => 'El campo phone_number es obligatorio',
            'phone_number.string' => 'El campo phone_number debe ser una cadena de caracteres.',
            'password.required' => 'El campo password es obligatorio',
            'password.string' => 'El campo password debe ser una cadena de caracteres.',
            'device_name.required' => 'El campo device_name es obligatorio',
            'device_name.string' => 'El campo device_name debe ser una cadena de caracteres.',
        ];
    }
}
