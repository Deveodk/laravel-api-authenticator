<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTManager;

class JwtService
{
    /** @var JWTManager */
    private $jwtManager;

    /** @var JWTAuth */
    private $jwtAuth;

    /**
     * JwtService constructor.
     * @param JWTManager $jwtManager
     * @param JWTAuth $jwtAuth
     */
    public function __construct(JWTManager $jwtManager, JWTAuth $jwtAuth)
    {
        $this->jwtManager = $jwtManager;
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * @param $payload
     * @return \Tymon\JWTAuth\Token
     */
    public function encode($payload)
    {
        return $this->jwtManager->encode($payload);
    }

    /**
     * @param $id
     * @param $model
     * @param null $ttl
     * @param null $type
     * @return \Tymon\JWTAuth\Payload
     */
    public function make($id, $model, $ttl = null, $type = null)
    {
        $manager = $this->jwtManager
            ->getPayloadFactory()
            ->addClaim('sub', $id)
            ->addClaim('model', $model);

        if ($ttl) {
            $manager->setTTL($ttl);
        }

        if ($type) {
            $manager->addClaim('type', $type);
        }
        return $manager->make();
    }

    /**
     * Get the JWT token from the header
     *
     * @return bool|string
     */
    public function getToken()
    {
        return $this->jwtAuth->getToken();
    }

    /**
     * Get the request payload
     *
     * @param $token
     * @return \Tymon\JWTAuth\Payload
     */
    public function getPayload($token)
    {
        return $this->jwtAuth->getPayload($token);
    }

    /**
     * Invalidate the token
     *
     * @param $token
     * @return bool
     */
    public function invalidate($token)
    {
        return $this->jwtAuth->invalidate($token);
    }
}
