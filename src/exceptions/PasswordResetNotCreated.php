<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordResetNotCreated extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, trans('apiAuth.exceptions.passwordResetNotCreated'));
    }
}