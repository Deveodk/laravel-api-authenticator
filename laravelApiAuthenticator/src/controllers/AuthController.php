<?php

namespace DeveoDK\LaravelApiAuthenticator\Controllers;

use DeveoDK\LaravelApiAuthenticator\Requests\AuthRequest;
use DeveoDK\LaravelApiAuthenticator\Services\ApiAuthenticatorService;
use DeveoDK\LaravelApiAuthenticator\Services\OptionService;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthInvalidatedTransformer;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthorizedTransformer;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthRefreshedTransformer;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Http\BaseController;

class AuthController extends BaseController
{
    /** @var ApiAuthenticatorService */
    private $apiAuthenticatorService;

    /** @var OptionService */
    private $optionService;

    public function __construct(
        ApiAuthenticatorService $apiAuthenticatorService,
        OptionService $optionService
    ) {
        $this->apiAuthenticatorService = $apiAuthenticatorService;
        $this->optionService = $optionService;
        $this->apiAuthenticatorService->setModels($optionService->get('authenticationModels'));
    }

    /**
     * Authenticate the from email and password
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function authenticate(AuthRequest $request)
    {
        $data = $this->apiAuthenticatorService->authenticate($request->data());

        return response()
            ->json($this->apiAuthenticatorService->setTransformer(new AuthorizedTransformer())->transformItem($data))
            ->header('Authorization', $data);
    }

    /**
     * Get the authenticated user
     * @return JsonResponse
     */
    public function authenticatedUser()
    {
        $data = $this->apiAuthenticatorService->getUser();
        return response()
            ->json($this->apiAuthenticatorService->setTransformer(new AuthTransformer())->transformItem($data));
    }

    /**
     * The actual refresh process gets handled in the middleware
     * @param Request $request
     * @return array
     */
    public function authenticatedRefresh(Request $request)
    {
        $this->apiAuthenticatorService->refreshToken();
        $token = str_replace("Bearer ", "", $request->header('authorization'));
        return $this->apiAuthenticatorService->setTransformer(new AuthRefreshedTransformer())->transformItem($token);
    }

    /**
     * Invalidate a JWT token
     * @param Request $request
     * @return array
     */
    public function authenticatedInvalidate(Request $request)
    {
        $this->apiAuthenticatorService->invalidateToken();
        $token = str_replace("Bearer ", "", $request->header('authorization'));
        return $this->apiAuthenticatorService->setTransformer(new AuthInvalidatedTransformer())->transformItem($token);
    }
}
