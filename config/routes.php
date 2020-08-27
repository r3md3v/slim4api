<?php

use App\Action\Auth\LogoutAction;
use App\Action\Auth\TokenCreateAction;
use App\Action\Auth\TokenListAction;
use App\Action\CustomerCreateAction;
use App\Action\CustomerListAction;
use App\Action\CustomerReadAction;
use App\Action\CustomerSearchAction;
use App\Action\Docs\SwaggerUiAction;
use App\Action\HelloAction;
use App\Action\HomeAction;
use App\Action\UserCreateAction;
use App\Action\UserListAction;
use App\Action\UserReadAction;
use App\Action\UserSearchAction;
use App\Middleware\JwtAuthMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

//use App\Middleware\UserAuthMiddleware;
//use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // CORS Pre-Flight OPTIONS Request Handler
    $app->options('/{routes:.*}', function (RequestInterface $request, ResponseInterface $response) {
        return $response;
    });

    // JWT oauth2 API login. This route must not be protected.
    $app->post('/tokens', TokenCreateAction::class);
    //$app->get('/tokens', TokenListAction::class); // to build = list of existing tokens
    $app->get('/logout', LogoutAction::class);

    // Home
    $app->get('/', HomeAction::class)->setName('root');
    $app->get('/status', HomeAction::class);

    // Hello - group1
    $app->group('/hello', function (Group $group1) {
        $group1->get('', HelloAction::class)->setName('hello');
        //$group1->get('/', \App\Action\HelloAction::class)->setName('hello'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group1->get('/{name}', HelloAction::class)->setName('hello');
    });

    // Customers - group2
    $app->group('/customers', function (Group $group2) {
        $group2->get('', CustomerListAction::class)->setName('customer-list');
        //$group2->get('/', \App\Action\CustomerListAction::class)->setName('customer-list'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group2->get('/id/{id:[0-9]+}', CustomerReadAction::class);
        $group2->get('/search/{keyword}', CustomerSearchAction::class);
        $group2->post('', CustomerCreateAction::class);
    });

    // Users - group3
    $app->group('/users', function (Group $group3) {
        $group3->get('', UserListAction::class)->setName('user-list');
        // $group3->get('/', UserListAction::class)->setName('user-list'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group3->get('/id/{id:[0-9]+}', UserReadAction::class);
        $group3->get('/search/{keyword}', UserSearchAction::class);
        $group3->post('', UserCreateAction::class);
        //});
    })->add(JwtAuthMiddleware::class);

    // Docs - Swagger
    $app->get('/docs/v1', SwaggerUiAction::class);

    // Route examples
    // $app->get('/news[/{year}[/{month}]]', function ($request, $response, $args) { // Multiple optionnal parameters
    // $app->get('/news[/{params:.*}]', function ($request, $response, $args) { // Unlimited optional parameters $params = explode('/', $args['params']);

    /*
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * NOTE: make sure this route is defined last
     */
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response): void {
        throw new Slim\Exception\HttpNotFoundException($request);
    });
};
