<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ToManyMagicLink extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, 'There have been to many attempts to generate the magic link');
    }
}