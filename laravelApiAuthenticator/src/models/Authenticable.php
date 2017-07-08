<?php

namespace DeveoDK\LaravelApiAuthenticator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;

abstract class Authenticable extends Model
{
    use Notifiable;

    public function authenticateAttempts()
    {
        return $this->morphMany(JwtAuthenticateAttempt::class, 'authenticable');
    }

    /**
     * Return a collection of blacklisted tokens for model
     * @return MorphMany
     */
    public function blacklists()
    {
        return $this->morphMany(JwtBlacklist::class, 'authenticable');
    }

    /**
     * Return a collection of refreshed tokens for model
     * @return MorphMany
     */
    public function refreshes()
    {
        return $this->morphMany(JwtRefreshToken::class, 'authenticable');
    }

    /**
     * Return a collection of tokens for model
     * @return MorphMany
     */
    public function tokens()
    {
        return $this->morphMany(JwtToken::class, 'authenticable');
    }
}
