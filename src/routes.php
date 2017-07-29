<?php

use DeveoDK\LaravelApiAuthenticator\Controllers\MagicLinkController;
use DeveoDK\LaravelApiAuthenticator\Controllers\AuthController;
use DeveoDK\LaravelApiAuthenticator\Models\ResetController;
use DeveoDK\LaravelApiAuthenticator\Models\SocialController;
use Illuminate\Routing\Router;

$route = app(Router::class);

$route->post('auth/accounts', AuthController::class.'@accounts');

$route->post('auth/login', AuthController::class.'@authenticate');

$route->post('auth/logout', AuthController::class.'@authenticatedInvalidate')->middleware('jwt.hasToken');

$route->get('auth/user', AuthController::class.'@authenticatedUser')->middleware('jwt.validToken');

$route->get('auth/refresh', AuthController::class.'@authenticatedRefresh')->middleware('jwt.hasToken');

$route->post('auth/magic_link', MagicLinkController::class.'@generateLink');

$route->post('auth/magic_link/authenticate', MagicLinkController::class.'@authenticateLink');

$route->post('auth/reset_password', ResetController::class.'@generateLink');

$route->get('auth/reset_password/validate/{token}', ResetController::class.'@validateToken');

$route->post('auth/reset_password/reset', ResetController::class.'@resetPassword');

$route->post('auth/facebook/validate', SocialController::class.'@facebookCallback');

$route->post('auth/facebook', SocialController::class.'@facebookAuthenticate');

$route->post('auth/google/validate', SocialController::class.'@googleCallback');

$route->post('auth/google', SocialController::class.'@googleAuthenticate');
