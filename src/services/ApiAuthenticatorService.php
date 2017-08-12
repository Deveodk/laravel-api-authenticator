<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Carbon\Carbon;
use DeveoDK\LaravelApiAuthenticator\events\AuthenticateAttempt;
use DeveoDK\LaravelApiAuthenticator\events\TokenInvalidated;
use DeveoDK\LaravelApiAuthenticator\events\TokenRefreshed;
use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\AccountNotFoundException;
use DeveoDK\LaravelApiAuthenticator\Exceptions\MagicLinkNotCreated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\TokenNotInvalidated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\TokenNotRefreshed;
use DeveoDK\LaravelApiAuthenticator\Exceptions\ToManyMagicLink;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotFoundException;
use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;
use DeveoDK\LaravelApiAuthenticator\Models\JwtMagicLink;
use DeveoDK\LaravelApiAuthenticator\Notifications\MagicLink;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use ReflectionClass;

class ApiAuthenticatorService extends BaseService
{
    /** @var JwtService */
    private $jwtService;

    /** @var Hasher */
    private $hasher;

    /** @var ChannelManager */
    private $notification;

    /** @var RequestService */
    private $requestService;

    /** @var ReflectionService */
    private $reflectionService;

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        JwtService $jwtService,
        Hasher $hasher,
        ChannelManager $notification,
        RequestService $requestService,
        ReflectionService $reflectionService
    ) {
        $this->requestService = $requestService;
        $this->notification = $notification;
        $this->hasher = $hasher;
        $this->jwtService = $jwtService;
        $this->reflectionService = $reflectionService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    //READ
    public function retrieveResource($query)
    {
        if (!$query) {
            throw new UserNotFoundException();
        }
        return $query;
    }

    /**
     * Get a list of accounts for multi tenancy auth
     *
     * @param $params
     * @return array
     */
    public function accounts($params)
    {
        $models = $this->optionService->get('authenticationModels');

        $collection = new Collection();

        foreach ($models as $model) {
            /** @var Authenticable $match */
            $match = (new $model())->where('email', '=', $params['email'])->first();

            if (!$match) {
                continue;
            }

            $label = (new ReflectionClass($match))->getShortName();

            $labelName = $label;
            if (trans('apiAuth.authenticableCustomLabel.'.$label) !== 'apiAuth.authenticableCustomLabel.'.$label) {
                $labelName = trans('apiAuth.authenticableCustomLabel.'.$label);
            }

            $match['label'] = $labelName;

            $collection->prepend($match);
        }

        $count = count($collection->all());

        if ($count === 0) {
            throw new AccountNotFoundException();
        }

        return $collection->all();
    }

    /**
     * @param $params
     * @return \Tymon\JWTAuth\Token|false
     */
    public function authenticate($params)
    {
        // Do necessary reflections
        $model = $this->reflectionService->getModelInstanceFromResponse($params);

        if (!$model) {
            return false;
        }

        $user = (new $model())->where('email', '=', $params['email'])->first();

        if (!$user) {
            $this->dispatcher->fire(new AuthenticateAttempt($params['email'], $model));
            throw new UserNotAuthenticated();
        }

        if (!$this->hasher->check($params['password'], $user->password)) {
            $this->dispatcher->fire(new AuthenticateAttempt($params['email'], $model));
            throw new UserNotAuthenticated();
        }

        $token = $this->generateAuthenticateToken($user->id, $model);

        $this->dispatcher->fire(new UserWasAuthenticated($token));

        return $token;
    }

    /**
     * Generate a authenticate token
     *
     * @param $id
     * @param $model
     * @return \Tymon\JWTAuth\Token
     */
    public function generateAuthenticateToken($id, $model)
    {
        $payload = $this->jwtService->make($id, $model);
        return $this->jwtService->encode($payload);
    }

    /**
     * @param $token
     * @return mixed
     */
    public function getAuthenticableFromToken($token)
    {
        $payload = json_decode($this->jwtService->getPayload($token));

        // Do necessary reflections
        $model = $this->reflectionService->getModelInstanceFromPayload($payload);
        if (!$model) {
            return false;
        }

        return (new $model())->findOrFail($payload->sub);
    }

    /**
     * @return Model|bool
     */
    public function getUser()
    {
        $token = $this->jwtService->getToken();
        $payload = json_decode($this->jwtService->getPayload($token));
        // Do necessary reflections
        $model = $this->reflectionService->getModelInstanceFromPayload($payload);
        if (!$model) {
            return false;
        }

        return (new $model())->findOrFail($payload->sub);
    }

    /**
     * @return boolean
     */
    public function invalidateToken()
    {
        if (!$token = $this->jwtService->getToken()) {
            throw new TokenNotInvalidated();
        }

        $payload = json_decode($this->jwtService->getPayload($token));

        $model = $this->reflectionService->getModelInstanceFromPayload($payload);
        if (!$model) {
            return false;
        }

        $this->dispatcher->fire(new TokenInvalidated((new $model())->findOrFail($payload->sub)));
        $this->jwtService->invalidate($token);

        return true;
    }

    /**
     * Dispatch a token refreshed event
     */
    public function refreshToken()
    {
        try {
            $token = $this->jwtService->parseToken()->refresh();
            $this->dispatcher->fire(new TokenRefreshed($token));
        } catch (Exception $exception) {
            throw new TokenNotRefreshed();
        }

        return $token;
    }
}
