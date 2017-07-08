<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\Models\JwtBlacklist;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class BlacklistService extends BaseService
{
    /** @var Request */
    private $request;

    /** @var RequestService */
    private $requestService;

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        Request $request,
        RequestService $requestService
    ) {
        $this->request = $request;
        $this->requestService = $requestService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    /**
     * @param $model
     * @param $id
     * @param $token
     * @param $key
     */
    public function create($model, $id, $token, $key)
    {
        $authenticableModel = (new $model)->where('id', '=', $id)->first();

        $log = new JwtBlacklist();
        $log->token = $token;
        $log->key = $key;
        $log->user_agent = $this->requestService->getUserAgent();
        $log->ip = $this->requestService->getRequestIp();

        if ($authenticableModel) {
            $log->authenticable()->associate($authenticableModel);
        }

        $log->save();
    }
}
