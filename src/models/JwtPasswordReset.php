<?php

namespace DeveoDK\LaravelApiAuthenticator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JwtPasswordReset extends Model
{
    use SoftDeletes;

    /**
     * Return the related authenticable model
     * @return MorphTo
     */
    public function authenticable()
    {
        return $this->morphTo('authenticable');
    }
}
