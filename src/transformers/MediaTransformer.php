<?php

namespace DeveoDK\LaravelApiAuthenticator\Transformers;

use League\Fractal;
use Spatie\MediaLibrary\Media;

class MediaTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = [];

    public function transform(Media $data)
    {
        return [
            'url' => $data->getUrl(),
            'size' => $data->getHumanReadableSizeAttribute()
        ];
    }
}
