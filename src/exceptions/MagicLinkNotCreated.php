<?php
namespace DeveoDK\LaravelApiAuthenticator\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MagicLinkNotCreated extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, 'The magic link was not created');
    }
}