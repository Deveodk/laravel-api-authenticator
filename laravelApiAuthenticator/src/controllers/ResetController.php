<?php

namespace DeveoDK\LaravelApiAuthenticator\Models;

use DeveoDK\LaravelApiAuthenticator\Exceptions\PasswordResetTokenInvalid;
use DeveoDK\LaravelApiAuthenticator\Requests\MakePasswordResetLink;
use DeveoDK\LaravelApiAuthenticator\Requests\PasswordReset;
use DeveoDK\LaravelApiAuthenticator\Services\ResetService;
use DeveoDK\LaravelApiAuthenticator\Transformers\AuthorizedTransformer;
use DeveoDK\LaravelApiAuthenticator\Transformers\ValidTokenTransformer;
use Illuminate\Http\JsonResponse;
use Infrastructure\Http\BaseController;

class ResetController extends BaseController
{
    /** @var ResetService */
    private $resetService;

    public function __construct(ResetService $resetService)
    {
        $this->resetService = $resetService;
    }

    /**
     * @param MakePasswordResetLink $request
     */
    public function generateLink(MakePasswordResetLink $request)
    {
        $this->resetService->generatePasswordResetLink($request->data());
    }

    /**
     * Check if the token i valid
     *
     * @param $token
     * @return JsonResponse
     */
    public function validateToken($token)
    {
        $valid = $this->resetService->validateToken($token);

        if (!$valid) {
            throw new PasswordResetTokenInvalid();
        }

        return response()
            ->json($this->resetService->setTransformer(new ValidTokenTransformer())->transformItem($token))
            ->header('Authorization', $token);
    }

    /**
     * Reset the password
     *
     * @param PasswordReset $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordReset $request)
    {
        $token = $this->resetService->resetPassword($request->data());

        return response()
            ->json($this->resetService->setTransformer(new AuthorizedTransformer())->transformItem($token))
            ->header('Authorization', $token);
    }
}
