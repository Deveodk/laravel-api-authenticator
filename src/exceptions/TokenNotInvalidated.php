<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenNotInvalidated extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, trans('apiAuth.exceptions.tokenNotInvalidated'));
    }
}