<?php

namespace DeveoDK\LaravelApiAuthenticator\Middleware;

use Closure;

use DeveoDK\LaravelApiAuthenticator\Exceptions\UserHasNoPermission;
use DeveoDK\LaravelApiAuthenticator\Services\ApiAuthenticatorService;
use DeveoDK\LaravelApiAuthenticator\Services\JwtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class HasRole
{
    /** @var JwtService */
    private $jwtService;

    /** @var ApiAuthenticatorService */
    private $apiAuthenticatorService;

    /**
     * HasToken constructor.
     * @param JwtService $jwtService
     * @param ApiAuthenticatorService $apiAuthenticatorService
     */
    public function __construct(JwtService $jwtService, ApiAuthenticatorService $apiAuthenticatorService)
    {
        $this->apiAuthenticatorService = $apiAuthenticatorService;
        $this->jwtService = $jwtService;
    }


    /**
     * @param $request
     * @param Closure $next
     * @param $role
     * @param null $permission
     * @return RedirectResponse|Redirector|mixed
     */
    public function handle($request, Closure $next, $role, $permission = null)
    {
        return app(ValidToken::class)->handle($request, function ($request) use ($next, $role, $permission) {

            $token = $this->jwtService->getToken();
            $user = $this->apiAuthenticatorService->getAuthenticableFromToken($token);

            $role = is_array($role)
                ? $role
                : explode('|', $role);

            if (! $user->hasAnyRole($role)) {
                throw new UserHasNoPermission();
            }

            if ($permission && ! $user->can($permission)) {
                throw new UserHasNoPermission();
            }

            return $next($request);
        });
    }
}
