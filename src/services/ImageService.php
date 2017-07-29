<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;

class ImageService
{
    /**
     * @param Authenticable $authenticable
     * @param $filename
     * @return bool
     */
    public function imageExist(Authenticable $authenticable, $filename)
    {
        $mediaCollections = $authenticable->getMedia('profile_pictures');

        foreach ($mediaCollections as $mediaCollection) {
            if ($mediaCollection->file_name === $filename) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Authenticable $authenticable
     * @param $url
     */
    public function createNewImageFromUrl(Authenticable $authenticable, $url)
    {
        $authenticable->addMediaFromUrl($url)
            ->toMediaCollection('profile_pictures');
    }

    /**
     * @param $url
     * @return string
     */
    public function getFilenameFromUrl($url)
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }
}
