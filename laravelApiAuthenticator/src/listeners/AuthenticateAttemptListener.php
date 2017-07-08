<?php

namespace DeveoDK\LaravelApiAuthenticator\Listeners;

use DeveoDK\LaravelApiAuthenticator\Services\AuthenticateAttemptService;

class AuthenticateAttemptListener
{
    /** @var AuthenticateAttemptService */
    private $authenticateAttemptService;

    /**
     * authenticateAttemptListener constructor.
     * @param AuthenticateAttemptService $authenticateAttemptService
     */
    public function __construct(AuthenticateAttemptService $authenticateAttemptService)
    {
        $this->authenticateAttemptService = $authenticateAttemptService;
    }

    /**
     * Handle the event.
     * @param $data
     */
    public function handle($data)
    {
        $this->authenticateAttemptService->create($data->model, $data->email);
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
