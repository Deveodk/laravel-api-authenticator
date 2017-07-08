<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use League\Fractal;

class AuthRefreshedTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [];

    public function transform($data)
    {
        return [
            'success' => [
                'status' => (int) 200,
                'token' => (string) $data,
                'message' => (string) "Token refreshed successfully"
            ],
        ];
    }
}
