<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;
use ReflectionClass;

class ReflectionService
{
    /** @var OptionService */
    private $optionService;

    /**
     * ReflectionService constructor.
     * @param OptionService $optionService
     */
    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    /**
     * @param $params
     * @return bool|string
     */
    public function getModelInstanceFromResponse($params)
    {
        $modelName = (isset($params['model'])) ? $params['model'] :
            (new ReflectionClass($this->optionService->get('defaultAuthenticationModel')))->getShortName();

        return $this->getAuthenticableFromModelName($modelName);
    }

    /**
     * @param $payload
     * @return bool|string
     */
    public function getModelInstanceFromPayload($payload)
    {
        $modelName = (isset($payload->model)) ? $payload->model :
            (new ReflectionClass($this->optionService->get('defaultAuthenticationModel')))->getShortName();

        return $this->getAuthenticableFromModelName($modelName);
    }

    /**
     * @param $modelName
     * @return bool|string
     */
    public function getAuthenticableFromModelName($modelName)
    {
        $authenticableModels = $this->optionService->get('authenticationModels');

        foreach ($authenticableModels as $authenticable) {
            if ((new ReflectionClass($authenticable))->getShortName() === $modelName) {
                return $authenticable;
            }

            continue;
        }

        return false;
    }
}
