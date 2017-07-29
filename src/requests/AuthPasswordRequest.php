<?php

namespace DeveoDK\LaravelApiAuthenticator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthPasswordRequest extends FormRequest
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
            'email' => 'email|required',
            'remember' => 'required',
            'password' => 'required'
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'model' => $this->model,
            'remember' => $this->remember,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
