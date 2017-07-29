<?php

namespace DeveoDK\LaravelApiAuthenticator\Listeners;

use DeveoDK\LaravelApiAuthenticator\Services\AuthenticateService;
use DeveoDK\LaravelApiAuthenticator\Services\JwtService;
use DeveoDK\LaravelApiAuthenticator\Services\OptionService;
use DeveoDK\LaravelApiAuthenticator\Services\ReflectionService;

class AuthenticateListener
{
    /** @var AuthenticateService */
    private $authenticateService;

    /** @var JwtService */
    private $jwtService;

    /** @var OptionService */
    private $optionService;

    /** @var ReflectionService */
    private $reflectionService;

    /**
     * AuthenticateListener constructor.
     * @param AuthenticateService $authenticateService
     * @param JwtService $jwtService
     * @param OptionService $optionService
     * @param ReflectionService $reflectionService
     */
    public function __construct(
        AuthenticateService $authenticateService,
        JwtService $jwtService,
        OptionService $optionService,
        ReflectionService $reflectionService
    ) {
        $this->jwtService = $jwtService;
        $this->authenticateService = $authenticateService;
        $this->optionService = $optionService;
        $this->reflectionService = $reflectionService;
    }

    /**
     * Handle the event.
     * @param $event
     * @return bool
     */
    public function handle($event)
    {
        $payload = $this->jwtService->getPayload($event->data);
        $data = json_decode($payload);

        $model = $this->reflectionService->getModelInstanceFromPayload($data);
        if (!$model) {
            return false;
        }

        $this->authenticateService->create($model, $data->sub, $event->data);

        return true;
    }

    /**
     * Handle an failed event.
     * @param $data
     * @param $exception
     */
    public function failed($data, $exception)
    {
        //
    }
}
