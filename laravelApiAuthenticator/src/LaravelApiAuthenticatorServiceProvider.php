<?php

namespace DeveoDK\LaravelApiAuthenticator;

use DeveoDK\LaravelApiAuthenticator\Middleware\HasToken;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Spatie\Fractal\FractalServiceProvider;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Middleware\RefreshToken;
use Tymon\JWTAuth\Providers\JWTAuthServiceProvider;

class LaravelApiAuthenticatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('api.authenticator.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../translations/apiAuth.php' => resource_path('lang/en/apiAuth.php'),
        ]);

        $this->app->register(
            FractalServiceProvider::class
        );
        $this->app->register(
            JWTAuthServiceProvider::class
        );

        $router->aliasMiddleware('jwt.hasToken', HasToken::class);

        $this->app->register(
            EventServiceProvider::class
        );
        require __DIR__ . '/routes.php';

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    /**
     * Register migration files.
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        return;
    }
}
