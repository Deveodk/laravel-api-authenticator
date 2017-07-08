<?php

namespace DeveoDK\LaravelApiAuthenticator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JwtAuthenticate extends Model
{

    /**
     * Return the related authenticable model
     * @return MorphTo
     */
    public function authenticable()
    {
        return $this->morphTo('authenticable');
    }
}
