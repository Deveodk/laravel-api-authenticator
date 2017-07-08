<?php

namespace DeveoDK\LaravelApiAuthenticator\Listeners;

use DeveoDK\LaravelApiAuthenticator\Services\AuthenticateService;
use DeveoDK\LaravelApiAuthenticator\Services\JwtService;
use DeveoDK\LaravelApiAuthenticator\Services\OptionService;

class AuthenticateListener
{
    /** @var AuthenticateService */
    private $authenticateService;

    /** @var JwtService */
    private $jwtService;

    /** @var OptionService */
    private $optionService;

    /**
     * AuthenticateListener constructor.
     * @param AuthenticateService $authenticateService
     * @param JwtService $jwtService
     * @param OptionService $optionService
     */
    public function __construct(
        AuthenticateService $authenticateService,
        JwtService $jwtService,
        OptionService $optionService
    ) {
        $this->jwtService = $jwtService;
        $this->authenticateService = $authenticateService;
        $this->optionService = $optionService;
    }

    /**
     * Handle the event.
     * @param $event
     */
    public function handle($event)
    {
        $payload = $this->jwtService->getPayload($event->data);
        $data = json_decode($payload);

        $model = (isset($data->model)) ? $data->model : $this->optionService->get('defaultAuthenticationModel');

        $this->authenticateService->create($model, $data->sub, $event->data);
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
