<?php

namespace Src\Utils\Foundation\Http;

use Src\Utils\Foundation\Http\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class APIRequest extends FormRequest
{
    /**
     * Determine if user authorized to make this request
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * If validator fails return the exception in json form
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = array_map(function ($error) {
            return ['mensaje' => $error];
        }, $validator->errors()->all());

        throw new HttpResponseException(ApiResponse::json(null, Response::HTTP_UNPROCESSABLE_ENTITY, $errors));
    }

    abstract public function rules();
}
