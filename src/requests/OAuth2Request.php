<?php

namespace DeveoDK\LaravelApiAuthenticator\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OAuth2Request extends FormRequest
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
            'code' => 'required',
            'redirect_url' => 'required'
        ];
    }

    /**
     * Give the data back
     * @return array
     */
    public function data()
    {
        return [
            'code' => $this->code,
            'redirect_url' => $this->redirect_url
        ];
    }
}
