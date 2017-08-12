<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenNotRefreshed extends HttpException
{
    public function __construct()
    {
        parent::__construct(401, trans('apiAuth.exceptions.tokenNotRefreshed'));
    }
}