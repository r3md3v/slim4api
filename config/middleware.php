<?php

use App\Middleware\CorsMiddleware;
use App\Middleware\DomainExceptionMiddleware;
use App\Middleware\HttpExceptionMiddleware;
use App\Middleware\HttpsMiddleware;
use App\Middleware\JwtClaimMiddleware;
use App\Middleware\TrailingSlashMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Redirect HTTP traffic to HTTPS
    $app->add(HttpsMiddleware::class);

    // Add the CORS built-in Middleware
    $app->add(CorsMiddleware::class);

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Add the BasepathMiddleware
    $app->add(BasePathMiddleware::class);

    // Add the JWTMiddleware
    $app->add(JwtClaimMiddleware::class);

    // Catch all http errors
    $app->add(HttpExceptionMiddleware::class);

    // Catch all domain errors
    $app->add(DomainExceptionMiddleware::class);

    // Catch all validation errors
    $app->add(ValidationExceptionMiddleware::class);

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);

    // Add/Remove the trailing slash (true/false)
    $app->add(TrailingSlashMiddleware::class);
};
