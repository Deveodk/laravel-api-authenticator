<?php

namespace DeveoDK\LaravelApiAuthenticator\events;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Event
{
    /** @var string */
    public $email;

    /** @var Model */
    public $model;

    /**
     * AuthenticateAttempt constructor.
     * @param $email
     * @param $model
     */
    public function __construct($email, $model)
    {
        $this->email = $email;
        $this->model = $model;
    }
}
