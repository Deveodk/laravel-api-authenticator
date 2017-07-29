<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use League\Fractal;

class OAuth2Transformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [];

    public function transform($data)
    {
        return [
            'email' => $data['email'],
            'access_token' => $data['access_token']
        ];
    }
}
