<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\Models\JwtAuthenticate;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Notifications\ChannelManager;

class AuthenticateService extends BaseService
{
    /** @var Request */
    private $request;

    /** @var ChannelManager */
    private $notification;

    /** @var RequestService */
    private $requestService;

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        Request $request,
        ChannelManager $notification,
        RequestService $requestService
    ) {
        $this->notification = $notification;
        $this->request = $request;
        $this->requestService = $requestService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    /**
     * @param $model
     * @param $id
     * @param $token
     */
    public function create($model, $id, $token)
    {
        $authenticableModel = (new $model)->where('id', '=', $id)->first();

        $log = new JwtAuthenticate();
        $log->token = $token;
        $log->user_agent = $this->requestService->getUserAgent();
        $log->ip = $this->requestService->getRequestIp();

        if ($authenticableModel) {
            $log->authenticable()->associate($authenticableModel);
        }

        $log->save();
    }
}
