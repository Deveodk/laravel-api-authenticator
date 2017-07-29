<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;

class OAuth2Service extends BaseService
{
    /** @var RequestService */
    private $requestService;

    /** @var ReflectionService */
    private $reflectionService;

    /**
     * ResetService constructor.
     * @param Dispatcher $dispatcher
     * @param DatabaseManager $database
     * @param OptionService $optionService
     * @param RequestService $requestService
     * @param ReflectionService $reflectionService
     */
    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        RequestService $requestService,
        ReflectionService $reflectionService
    ) {
        $this->requestService = $requestService;
        $this->reflectionService = $reflectionService;
        parent::__construct($dispatcher, $database, $optionService);
    }
}
