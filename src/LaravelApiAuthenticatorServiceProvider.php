<?php

namespace DeveoDK\LaravelApiAuthenticator;

use DeveoDK\LaravelApiAuthenticator\Middleware\HasRole;
use DeveoDK\LaravelApiAuthenticator\Middleware\HasToken;
use DeveoDK\LaravelApiAuthenticator\Middleware\ValidToken;
use Facebook\Facebook;
use Google_Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Spatie\Fractal\FractalServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
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
        $this->app->register(
            MediaLibraryServiceProvider::class
        );
        $this->app->register(
            PermissionServiceProvider::class
        );
        $this->app->register(
            EventServiceProvider::class
        );

        $router->aliasMiddleware('jwt.hasToken', HasToken::class);
        $router->aliasMiddleware('jwt.validToken', ValidToken::class);
        $router->aliasMiddleware('jwt.hasRole', HasRole::class);

        $this->loadRoutesFrom(__DIR__  . '/routes.php');

        require __DIR__ . '/../database/seeders/BasicRolesSeeder.php';

        require __DIR__ . '/helpers/AuthHelper.php';

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }

        $this->registerDependencies();
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

    /**
     * Register application dependencies
     */
    protected function registerDependencies()
    {
        $this->app->bind(Facebook::class, function (Application $application) {
            return new Facebook([
                'app_id' => env('FACEBOOK_APP_ID', null),
                'app_secret' => env('FACEBOOK_APP_SECRET', null),
                'default_graph_version' => Facebook::DEFAULT_GRAPH_VERSION,
            ]);
        });

        $this->app->bind(Google_Client::class, function (Application $application) {
            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_APP_ID'));
            $client->setClientSecret(env('GOOGLE_APP_SECRET'));
            return $client;
        });
    }
}
