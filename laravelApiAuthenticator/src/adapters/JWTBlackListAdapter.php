<?php

namespace DeveoDK\LaravelApiAuthenticator\Adapters;

use DeveoDK\LaravelApiAuthenticator\Models\JwtBlacklist;
use DeveoDK\LaravelApiAuthenticator\Services\BlacklistService;
use Illuminate\Cache\CacheManager;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Providers\Storage\StorageInterface;

class JWTBlackListAdapter implements StorageInterface
{
    /**
     * @var BlacklistService
     */
    protected $blacklistService;

    /** @var CacheManager */
    protected $cache;

    /**
     * @var string
     */
    protected $tag = 'deveo.authenticator';

    /**
     * @param CacheManager $cache
     * @param BlacklistService $blacklistService
     */
    public function __construct(
        CacheManager $cache,
        BlacklistService $blacklistService
    ) {
        $this->blacklistService = $blacklistService;
        $this->cache = $cache;
    }

    /**
     * Add a new item into storage.
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $minutes
     * @return void
     */
    public function add($key, $value, $minutes)
    {
        $token = JWTAuth::getToken();
        $payload = json_decode(JWTAuth::getPayload($token));

        $this->blacklistService->create($payload->model, $payload->sub, $token, $key);
    }

    /**
     * Check whether a key exists in storage.
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        $blackList = JwtBlacklist::where('key', $key)->first();
        if (!$blackList) {
            return false;
        }
        return true;
    }

    /**
     * Remove an item from storage.
     * @param  string  $key
     * @return bool
     */
    public function destroy($key)
    {
        return JwtBlacklist::where('key', $key)->delete();
    }

    /**
     * Remove all items associated with the key
     * @return void
     */
    public function flush()
    {
        JwtBlacklist::all()->delete();
    }

    /**
     * Return the cache instance with tags attached.
     *
     * @return \Illuminate\Cache\CacheManager
     */
    protected function cache()
    {
        if (! method_exists($this->cache, 'tags')) {
            return $this->cache;
        }

        return $this->cache->tags($this->tag);
    }
}
