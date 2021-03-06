<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;
use League\Fractal;

class AuthTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'profile_pictures'
    ];
    protected $availableIncludes = [];

    public function transform(Authenticable $data)
    {
        return [
            'id'      => (int) $data->id,
            'email' => (string) $data->email,
            'firstname' => (string) $data->firstname,
            'lastname' => (string) $data->lastname,
            'fullname' => (string) "".$data->firstname." ".$data->lastname."",
            'initials' => (string) str_limit($data->firstname, $limit = 1, $end = '').
                str_limit($data->lastname, $limit = 1, $end = ''),
            'roles' => $data->roles()->get(),
            'created_at' => (object) $data->created_at,
            'updated_at' => (object) $data->updated_at,
        ];
    }

    /**
     * @param Authenticable $data
     * @return array|Fractal\Resource\Collection
     */
    public function includeProfilePictures(Authenticable $data)
    {
        if (!empty($data->getMedia('profile_pictures'))) {
            return $this->collection($data->getMedia('profile_pictures'), new MediaTransformer());
        }

        return [];
    }
}
