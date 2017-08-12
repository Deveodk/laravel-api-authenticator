<?php

use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @
 */
if (! function_exists('user')) {
    function user()
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return false;
        }

        $payload = json_decode(JWTAuth::getPayload($token));

        $modelName = (isset($payload->model)) ? $payload->model :
            (new ReflectionClass(config('api.authenticator.defaultAuthenticationModel')))->getShortName();

        $authenticableModels = config('api.authenticator.authenticationModels');

        $model = false;

        foreach ($authenticableModels as $authenticable) {
            if ((new ReflectionClass($authenticable))->getShortName() === $modelName) {
                $model = $authenticable;
            }
            continue;
        }

        if (!$model) {
            return false;
        }

        return (new $model())->findOrFail($payload->sub);
    }
}
