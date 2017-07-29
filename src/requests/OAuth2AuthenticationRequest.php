<?php

namespace DeveoDK\LaravelApiAuthenticator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OAuth2AuthenticationRequest extends FormRequest
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
            'access_token' => 'required',
            'redirect_url' => 'required',
            'model' => 'required'
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'access_token' => $this->access_token,
            'redirect_url' => $this->redirect_url,
            'model' => $this->model
        ];
    }
}
