<?php

namespace DeveoDK\LaravelApiAuthenticator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthMagicLink extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'token' => 'required',
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'token' => $this->token
        ];
    }
}
