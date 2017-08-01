<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserHasNoPermission extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, trans('apiAuth.exceptions.userHasNoPermission'));
    }
}