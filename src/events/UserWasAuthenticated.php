<?php

namespace DeveoDK\LaravelApiAuthenticator\events;

class UserWasAuthenticated extends Event
{
    /** @var object */
    public $data;

    /**
    * Create a new event instance.
    * @param object
    */
    public function __construct($data)
    {
        $this->data = $data;
    }
}
