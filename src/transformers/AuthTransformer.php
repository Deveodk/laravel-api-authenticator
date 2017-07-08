<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use League\Fractal;

class AuthTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [];

    public function transform($data)
    {
        return [
            'id'      => (int) $data->id,
            'email' => (string) $data->email,
            'firstname' => (string) $data->firstname,
            'lastname' => (string) $data->lastname,
            'fullname' => (string) "".$data->firstname." ".$data->lastname."",
            'initials' => (string) str_limit($data->firstname, $limit = 1, $end = '').
                str_limit($data->lastname, $limit = 1, $end = ''),
            'created_at' => (object) $data->created_at,
            'updated_at' => (object) $data->updated_at,
        ];
    }
}
