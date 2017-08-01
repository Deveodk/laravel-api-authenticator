<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;
use Illuminate\Database\Eloquent\Model;
use League\Fractal;
use ReflectionClass;

class AuthAccountTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'profile_pictures'
    ];
    protected $availableIncludes = [];

    public function transform($data)
    {
        return [
            'model' => (new ReflectionClass($data))->getShortName(),
            'email' => (string) $data->email,
            'label' => (string) $data->label,
            'fullname' => (string) "".$data->firstname." ".$data->lastname."",
            'initials' => (string) str_limit($data->firstname, $limit = 1, $end = '').
                str_limit($data->lastname, $limit = 1, $end = '')
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
