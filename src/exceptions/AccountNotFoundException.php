<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AccountNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, trans('apiAuth.exceptions.accountNotFound'));
    }
}