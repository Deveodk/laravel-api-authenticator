<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Carbon\Carbon;
use DeveoDK\LaravelApiAuthenticator\Exceptions\ToManyAuthenticateAttemptsException;
use DeveoDK\LaravelApiAuthenticator\Models\JwtAuthenticateAttempt;
use DeveoDK\LaravelApiAuthenticator\Notifications\ToManyAuthenticateAttempt;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;

class AuthenticateAttemptService extends BaseService
{
    /** @var ChannelManager */
    private $notification;

    /** @var RequestService */
    private $requestService;

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        ChannelManager $notification,
        RequestService $requestService
    ) {
        $this->notification = $notification;
        $this->requestService = $requestService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    /**
     * Save an authentication attempt
     *
     * @param $model
     * @param $email
     */
    public function create($model, $email)
    {
        $authenticableModel = (new $model)->where('email', '=', $email)->first();

        $log = new JwtAuthenticateAttempt();
        $log->email = $email;
        $log->user_agent = $this->requestService->getUserAgent();
        $log->ip = $this->requestService->getRequestIp();

        if ($authenticableModel) {
            $this->checkThrottle($email, $authenticableModel);
            $log->authenticable()->associate($authenticableModel);
        }

        $log->save();
    }

    /**
     * Check if there have been to many login attempts
     * @param $email
     * @param $authenticableModel
     */
    public function checkThrottle($email, $authenticableModel)
    {
        $attemptCount = JwtAuthenticateAttempt::where('email', '=', $email)
            ->where('created_at', '>=', Carbon::now()->subHour(2))->count();

        // If there have been 15 attempts in the given period send email warning the user
        if ($attemptCount === 15) {
            $this->notification->send($authenticableModel, new ToManyAuthenticateAttempt());
        }

        // Throw a rate limit exception
        if ($attemptCount >= 15) {
            throw new ToManyAuthenticateAttemptsException();
        }
    }
}
