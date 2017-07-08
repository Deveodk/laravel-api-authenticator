<?php

namespace DeveoDK\LaravelApiAuthenticator\Controllers;

use DeveoDK\LaravelApiAuthenticator\Requests\AuthMagicLink;
use DeveoDK\LaravelApiAuthenticator\Requests\AuthMakeMagicLink;
use DeveoDK\LaravelApiAuthenticator\Services\ApiAuthenticatorService;
use DeveoDK\LaravelApiAuthenticator\Services\MagicLinkService;
use DeveoDK\LaravelApiAuthenticator\Services\OptionService;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthorizedTransformer;
use Infrastructure\Http\BaseController;

class MagicLinkController extends BaseController
{
    /** @var MagicLinkService */
    private $magicLinkService;

    /** @var OptionService */
    private $optionService;

    /** @var ApiAuthenticatorService */
    private $apiAuthenticatorService;

    public function __construct(
        MagicLinkService $magicLinkService,
        OptionService $optionService,
        ApiAuthenticatorService $apiAuthenticatorService
    ) {
        $this->apiAuthenticatorService = $apiAuthenticatorService;
        $this->magicLinkService = $magicLinkService;
        $this->optionService = $optionService;
    }

    /**
     * @param AuthMakeMagicLink $request
     */
    public function generateLink(AuthMakeMagicLink $request)
    {
        $this->magicLinkService->generateMagicLink($request->data());
    }

    /**
     * Authenticate link
     *
     * @param AuthMagicLink $request
     * @return string
     */
    public function authenticateLink(AuthMagicLink $request)
    {
        $magicToken = $request->data()['token'];
        $jwtToken = $this->magicLinkService->authenticateMagicLink($magicToken);

        return response()
            ->json($this->apiAuthenticatorService
                ->setTransformer(new AuthorizedTransformer())->transformItem($jwtToken))
            ->header('Authorization', $jwtToken);
    }
}
