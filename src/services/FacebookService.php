<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use DeveoDK\LaravelApiAuthenticator\events\UserWasAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Exceptions\UserNotAuthenticated;
use DeveoDK\LaravelApiAuthenticator\Models\Authenticable;
use Exception;
use Facebook\Facebook;
use Facebook\FacebookClient;
use Illuminate\Events\Dispatcher;

class FacebookService
{
    /** @var JwtService */
    private $jwtService;

    /** @var ReflectionService */
    private $reflectionService;

    /** @var FacebookClient */
    private $facebookClient;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var ImageService */
    private $imageService;

    public function __construct(
        JwtService $jwtService,
        ReflectionService $reflectionService,
        Facebook $facebook,
        Dispatcher $dispatcher,
        ImageService $imageService
    ) {
        $this->dispatcher = $dispatcher;
        $this->facebookClient = $facebook;
        $this->jwtService = $jwtService;
        $this->reflectionService = $reflectionService;
        $this->imageService = $imageService;
    }

    /**
     * @param $params
     * @return array
     */
    public function facebookCallback($params)
    {
        try {
            $accessToken = $this->facebookClient
            ->getOAuth2Client()
            ->getAccessTokenFromCode($params['code'], $params['redirect_url']);

            $userProfile = $this->facebookClient->get('/me?fields=email,verified', $accessToken);

            $response = $userProfile->getDecodedBody();
        } catch (Exception $exception) {
            throw new UserNotAuthenticated();
        }
        // Check if user email has been verified
        if (!$response['verified']) {
            throw new UserNotAuthenticated();
        }

        $data = [
            'email' => $response['email'],
            'access_token' => $accessToken->getValue()
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
            $userProfile = $this->facebookClient
                ->get('/me?fields=email,verified,picture.type(large)', $params['access_token']);
            $response = $userProfile->getDecodedBody();
        } catch (Exception $exception) {
            throw new UserNotAuthenticated();
        }
        $model = $this->reflectionService->getModelInstanceFromResponse($params);

        /** @var Authenticable $authenticable */
        $authenticable = (new $model)->where('email', '=', $response['email'])->first();

        $picture = (isset($response['picture'])) ? $response['picture'] : null;
        $pictureData = (isset($picture['data'])) ? $picture['data'] : null;

        if (!$pictureData['is_silhouette']) {
            $filename = $this->imageService->getFilenameFromUrl($pictureData['url']);

            if (!$this->imageService->imageExist($authenticable, $filename)) {
                $this->imageService->createNewImageFromUrl($authenticable, $pictureData['url']);
            }
        }

        $payload = $this->jwtService->make($authenticable->id, $model);
        $jwtToken = $this->jwtService->encode($payload);

        $this->dispatcher->fire(new UserWasAuthenticated($jwtToken));

        return $jwtToken;
    }
}
