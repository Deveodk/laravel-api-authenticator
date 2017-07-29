<?php

namespace DeveoDK\LaravelApiAuthenticator\Models;

use DeveoDK\LaravelApiAuthenticator\Requests\OAuth2AuthenticationRequest;
use DeveoDK\LaravelApiAuthenticator\Requests\OAuth2Request;
use DeveoDK\LaravelApiAuthenticator\Services\FacebookService;
use DeveoDK\LaravelApiAuthenticator\Services\GoogleService;
use DeveoDK\LaravelApiAuthenticator\Services\OAuth2Service;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthorizedTransformer;
use DeveoDK\LaravelApiAuthenticator\Transformers\OAuth2Transformer;
use Facebook\Facebook;

class SocialController
{
    /** @var Facebook */
    private $facebookClient;

    /** @var OAuth2Service */
    private $OAuth2Service;

    /** @var FacebookService */
    private $facebookService;

    /** @var GoogleService */
    private $googleService;

    /**
     * SocialController constructor.
     * @param Facebook $facebook
     * @param OAuth2Service $oAuth2Service
     * @param FacebookService $facebookService
     * @param GoogleService $googleService
     */
    public function __construct(
        Facebook $facebook,
        OAuth2Service $oAuth2Service,
        FacebookService $facebookService,
        GoogleService $googleService
    ) {
        $this->facebookService = $facebookService;
        $this->OAuth2Service = $oAuth2Service;
        $this->facebookClient = $facebook;
        $this->googleService = $googleService;
    }

    /**
     * @param OAuth2Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookCallback(OAuth2Request $request)
    {
        $params = $request->data();

        $data = $this->facebookService->facebookCallback($params);
        $transformed = $this->OAuth2Service->setTransformer(new OAuth2Transformer())->transformItem($data);

        return response()->json($transformed);
    }

    /**
     * @param OAuth2AuthenticationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookAuthenticate(OAuth2AuthenticationRequest $request)
    {
        $params = $request->data();

        $token = $this->facebookService->generateJwtFromBody($params);

        return response()
            ->json($this->OAuth2Service->setTransformer(new AuthorizedTransformer())->transformItem($token))
            ->header('Authorization', $token);
    }

    /**
     * @param OAuth2Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleCallback(OAuth2Request $request)
    {
        $params = $request->data();
        $data = $this->googleService->googleCallback($params);

        return response()->json($this->OAuth2Service->setTransformer(new OAuth2Transformer())->transformItem($data));
    }

    /**
     * @param OAuth2AuthenticationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleAuthenticate(OAuth2AuthenticationRequest $request)
    {
        $params = $request->data();

        $token = $this->googleService->generateJwtFromBody($params);

        return response()
            ->json($this->OAuth2Service->setTransformer(new AuthorizedTransformer())->transformItem($token))
            ->header('Authorization', $token);
    }
}
