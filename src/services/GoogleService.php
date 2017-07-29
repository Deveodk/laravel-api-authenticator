<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotAuthenticated;
use Exception;
use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Plus;
use Illuminate\Events\Dispatcher;

class GoogleService
{
    /** @var JwtService */
    private $jwtService;

    /** @var ReflectionService */
    private $reflectionService;

    /** @var Google_Client */
    private $googleClient;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var ImageService */
    private $imageService;

    public function __construct(
        JwtService $jwtService,
        ReflectionService $reflectionService,
        Google_Client $googleClient,
        Dispatcher $dispatcher,
        ImageService $imageService
    ) {
        $this->dispatcher = $dispatcher;
        $this->googleClient = $googleClient;
        $this->jwtService = $jwtService;
        $this->reflectionService = $reflectionService;
        $this->imageService = $imageService;
    }

    /**
     * @param $params
     * @return array
     */
    public function googleCallback($params)
    {
        try {
            $this->googleClient->setRedirectUri($params['redirect_url']);
            $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($params['code']);

            $response = (new Google_Service_Oauth2($this->googleClient))->userinfo->get();
        } catch (Exception $exception) {
            throw new UserNotAuthenticated();
        }
        // Check if user email has been verified
        if (!$response->verifiedEmail) {
            throw new UserNotAuthenticated();
        }

        $data = [
            'email' => $response->email,
            'access_token' => $accessToken['access_token']
        ];

        return $data;
    }

    /**
     * @param $params
     * @return \Tymon\JWTAuth\Token
     */
    public function generateJwtFromBody($params)
    {
        try {
            $this->googleClient->setRedirectUri($params['redirect_url']);
            $this->googleClient->setAccessToken($params['access_token']);

            $response = (new Google_Service_Oauth2($this->googleClient))->userinfo->get();
        } catch (Exception $exception) {
            throw new UserNotAuthenticated();
        }

        $model = $this->reflectionService->getModelInstanceFromResponse($params);

        $authenticable = (new $model)->where('email', '=', $response->email)->first();

        $filename = $this->imageService->getFilenameFromUrl($response->picture);
        if (!$this->imageService->imageExist($authenticable, $filename)) {
            $this->imageService->createNewImageFromUrl($authenticable, $response->picture);
        }

        $payload = $this->jwtService->make($authenticable->id, $model);
        $jwtToken = $this->jwtService->encode($payload);

        $this->dispatcher->fire(new UserWasAuthenticated($jwtToken));

        return $jwtToken;
    }
}
