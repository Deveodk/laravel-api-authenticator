<?php

namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, 'The User was not found.');
    }
}
