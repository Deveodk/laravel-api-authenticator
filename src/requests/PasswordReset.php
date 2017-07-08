<?php

namespace DeveoDK\LaravelApiAuthenticator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordReset extends FormRequest
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
            'password' => 'confirmed|required',
            'token' => 'required'
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'password' => $this->password,
            'token' => $this->token
        ];
    }
}
