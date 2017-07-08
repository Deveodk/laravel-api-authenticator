<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Carbon\Carbon;
use DeveoDK\LaravelApiAuthenticator\events\AuthenticateAttempt;
use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\MagicLinkNotCreated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\ToManyMagicLink;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Models\JwtMagicLink;
use DeveoDK\LaravelApiAuthenticator\Notifications\MagicLink;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager;

class MagicLinkService extends BaseService
{
    /** @var ChannelManager */
    private $notification;

    /** @var RequestService */
    private $requestService;

    /** @var JwtService */
    private $jwtService;

    public function __construct(
        Dispatcher $dispatcher,
        DatabaseManager $database,
        OptionService $optionService,
        ChannelManager $notification,
        RequestService $requestService,
        JwtService $jwtService
    ) {
        $this->jwtService = $jwtService;
        $this->notification = $notification;
        $this->requestService = $requestService;
        parent::__construct($dispatcher, $database, $optionService);
    }

    public function authenticateMagicLink($token)
    {
        $magicLink = JwtMagicLink::where('token', '=', $token)->first();

        if (!$magicLink) {
            throw new UserNotAuthenticated();
        }

        $authenticable = $magicLink->authenticable;

        $this->deleteOldToken($authenticable->id);

        $payload = $this->jwtService->make($authenticable->id, $magicLink->authenticable_type);
        $jwtToken = $this->jwtService->encode($payload);

        $this->dispatcher->fire(new UserWasAuthenticated($jwtToken));

        return $jwtToken;
    }

    /**
     * Generate a new magic link token
     *
     * @param $params
     * @return bool
     */
    public function generateMagicLink($params)
    {
        $email = $params['email'];
        $url = $params['url'];

        $model = (isset($params['model'])) ? $params['model'] : $this->optionService->get('defaultAuthenticationModel');

        $authenticable = (new $model())->where('email', '=', $email)->first();

        if (!$authenticable) {
            $this->dispatcher->fire(new AuthenticateAttempt($params['email'], $model));
            throw new MagicLinkNotCreated();
        }

        // Delete old magic links
        $this->deleteOldToken($authenticable->id);

        // Check for throttle
        $this->checkThrottle($params['email'], $model, $authenticable->id);

        $ttlInMinutes = Carbon::now()->addWeeks(2)->diffInMinutes();

        $payload = $this->jwtService->make($authenticable->id, $model, $ttlInMinutes);
        $token = $this->jwtService->encode($payload);

        // Save the magic link to the db
        $this->saveMagicLink($token, $authenticable);

        $magicLink = $url . '?token='.$token;

        $this->notification->send($authenticable, new MagicLink($magicLink));

        return true;
    }

    /**
     * @param $token
     * @param $authenticable
     */
    public function saveMagicLink($token, $authenticable)
    {
        $magicEntry = new JwtMagicLink();
        $magicEntry->token = $token;
        $magicEntry->user_agent = $this->requestService->getUserAgent();
        $magicEntry->ip = $this->requestService->getRequestIp();
        $magicEntry->authenticable()->associate($authenticable);
        $magicEntry->save();
    }

    /**
     * Check for throttle
     *
     * @param $email
     * @param $model
     * @param $id
     */
    public function checkThrottle($email, $model, $id)
    {
        $magicCount = JwtMagicLink::where('authenticable_id', '=', $id)->withTrashed()
            ->where('created_at', '>=', Carbon::now()->subHour(2))->count();

        // if there have been 5 magic link attempts the last two hours throw exception
        if ($magicCount >= 5) {
            $this->dispatcher->fire(new AuthenticateAttempt($email, $model));
            throw new ToManyMagicLink();
        }
    }

    /**
     * Delete old tokens
     *
     * @param $id
     */
    public function deleteOldToken($id)
    {
        JwtMagicLink::where('authenticable_id', '=', $id)->delete();
    }
}
