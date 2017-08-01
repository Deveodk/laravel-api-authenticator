<?php

namespace DeveoDK\LaravelApiAuthenticator\Adapters;

use Carbon\Carbon;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\Media;

class MediaPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media) : string
    {
        return Carbon::parse($media->created_at)->timestamp.'/';
    }
    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     * @return string
     */
    public function getPathForConversions(Media $media) : string
    {
        return $this->getPath($media).'c/';
    }
}
