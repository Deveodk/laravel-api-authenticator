<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Carbon\Carbon;
use DeveoDK\LaravelApiAuthenticator\events\AuthenticateAttempt;
use DeveoDK\LaravelApiAuthenticator\events\TokenInvalidated;
use DeveoDK\LaravelApiAuthenticator\events\TokenRefreshed;
use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\MagicLinkNotCreated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\TokenNotInvalidated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\ToManyMagicLink;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotFoundException;
use DeveoDK\LaravelApiAuthenticator\Models\JwtMagicLink;
use DeveoDK\LaravelApiAuthenticator\Notifications\MagicLink;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;

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

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        JwtService $jwtService,
        Hasher $hasher,
        ChannelManager $notification,
        RequestService $requestService
    ) {
        $this->requestService = $requestService;
        $this->notification = $notification;
        $this->hasher = $hasher;
        $this->jwtService = $jwtService;
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

    // Authenticate
    public function authenticate($params)
    {
        // Do necessary reflections
        $model = (isset($params['model'])) ? $params['model'] : $this->optionService->get('defaultAuthenticationModel');

        $user = (new $model())->where('email', '=', $params['email'])->first();

        if (!$user) {
            $this->dispatcher->fire(new AuthenticateAttempt($params['email'], $model));
            throw new UserNotAuthenticated();
        }

        if (!$this->hasher->check($params['password'], $user->password)) {
            $this->dispatcher->fire(new AuthenticateAttempt($params['email'], $model));
            throw new UserNotAuthenticated();
        }

        $this->generateAuthenticateToken($user->id, $model);

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
        $model = (isset($payload->model)) ? $payload->model : $this->optionService->get('defaultAuthenticationModel');

        return (new $model())->findOrFail($payload->sub);
    }

    /**
     * @return Model
     */
    public function getUser()
    {
        $token = $this->jwtService->getToken();
        $payload = json_decode($this->jwtService->getPayload($token));
        $model = (isset($payload->model)) ? $payload->model : $this->optionService->get('defaultAuthenticationModel');

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
        $model = (isset($payload->model)) ? $payload->model : $this->optionService->get('defaultAuthenticationModel');

        $this->dispatcher->fire(new TokenInvalidated((new $model())->findOrFail($payload->sub)));
        $this->jwtService->invalidate($token);

        return true;
    }

    /**
     * Dispatch a token refreshed event
     */
    public function refreshToken()
    {
        $token = $this->jwtService->getToken();
        $this->dispatcher->fire(new TokenRefreshed($token));
    }

    public function reset($email)
    {
        if (!$user = $this->getModel()->where('email', '=', $email)->first()) {
            throw new UserNotFoundException();
        }
        DB::table('password_resets')->where('email', '=', $user->email)->delete();
        $token = Uuid::generate(4);

        DB::table('password_resets')->insert(
            ['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]
        );
        Notification::send($user, new PasswordResetNotification($user, $token));

        return $this->SuccessTransformer();
    }

    public function CheckResetPasswordToken($token)
    {
        $reset = DB::table('password_resets')->where('token', '=', $token)->first();
        if(!$reset){
            throw new TokenNotFoundException();
        }
        return $this->SuccessTransformer();
    }

    public function ResetPassword($token, $params)
    {
        $reset = DB::table('password_resets')->where('token', '=', $token)->first();
        if (!$reset) {
            throw new TokenNotFoundException();
        }
        $this->getModel()
            ->where('email', '=', $reset->email)
            ->update($params);
        DB::table('password_resets')->where('token', '=', $token)->delete();
        return $this->SuccessTransformer();
    }
}
