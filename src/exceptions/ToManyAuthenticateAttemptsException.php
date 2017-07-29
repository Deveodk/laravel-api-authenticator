<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ToManyAuthenticateAttemptsException extends HttpException
{
    public function __construct()
    {
        parent::__construct(429, trans('apiAuth.exceptions.toManyLoginAttempts'));
    }
}