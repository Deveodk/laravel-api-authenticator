<?php

namespace DeveoDK\LaravelApiAuthenticator\Middleware;

use Closure;

use DeveoDK\LaravelApiAuthenticator\Services\JwtService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ValidToken
{
    /** @var JwtService */
    private $jwtService;

    /**
     * HasToken constructor.
     * @param JwtService $jwtService
     */
    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (!$token = $this->jwtService->getToken()) {
                return response()->json('token_absent', 401);
            }
            $this->jwtService->getPayload($token);
        } catch (TokenExpiredException $e) {
            return response()->json('token_expired', 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json('token_blacklisted', 401);
        } catch (TokenInvalidException $e) {
            return response()->json('token_invalid', 401);
        } catch (JWTException $e) {
            return response()->json('token_absent', 401);
        }

        return $next($request);
    }
}
