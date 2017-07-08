<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordResetTokenInvalid extends HttpException
{
    public function __construct()
    {
        parent::__construct(401, 'Password reset token was invalid');
    }
}