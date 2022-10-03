<?php

declare(strict_types=1);

namespace Src\Usuario\Auth\Infrastructure\Web;

use Src\Utils\Foundation\Http\APIRequest;

class AuthenticateRequest extends APIRequest
{
    public function rules()
    {
        return [
            'identity' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => 'El campo phone_number es obligatorio',
            'phone_number.string' => 'El campo phone_number debe ser una cadena de caracteres.',
            'password.required' => 'El campo password es obligatorio',
            'password.string' => 'El campo password debe ser una cadena de caracteres.',
        ];
    }
}
