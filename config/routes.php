<?php

//use App\Action\PreflightAction;
use App\Middleware\JwtAuthMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

//use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    // CORS Pre-Flight OPTIONS Request Handler
    $app->options('/{routes:.*}', function (RequestInterface $request, ResponseInterface $response) {
        return $response;
    });

    // JWT oauth2 API login. This route must not be protected.
    $app->post('/tokens', \App\Action\Auth\TokenCreateAction::class);
    $app->get('/tokens', \App\Action\Auth\TokenListAction::class); // to build = list of existing tokens
    $app->get('/logout', \App\Action\Auth\LogoutAction::class);

    // Home
    $app->get('/', \App\Action\HomeAction::class)->setName('root');
    $app->get('/status', \App\Action\HomeAction::class);

    // Hello - group1
    $app->group('/hello', function (Group $group1) {
        $group1->get('', \App\Action\HelloAction::class)->setName('hello');
        //$group1->get('/', \App\Action\HelloAction::class)->setName('hello'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group1->get('/{name}', \App\Action\HelloAction::class)->setName('hello');
    });

    // Customers - group2
    $app->group('/customers', function (Group $group2) {
        $group2->get('', \App\Action\CustomerListAction::class)->setName('customer-list');
        //$group2->get('/', \App\Action\CustomerListAction::class)->setName('customer-list'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group2->get('/id/{id:[0-9]+}', \App\Action\CustomerReadAction::class);
        $group2->get('/search/{keyword}', \App\Action\CustomerSearchAction::class);
        $group2->post('', \App\Action\CustomerCreateAction::class);
    });

    // Users - group3
    $app->group('/users', function (Group $group3) {
        $group3->get('', \App\Action\UserListAction::class)->setName('user-list');
        // $group3->get('/', \App\Action\UserListAction::class)->setName('user-list'); // trailing slash not recommanded => TrailingSlashMiddleware
        $group3->get('/id/{id:[0-9]+}', \App\Action\UserReadAction::class);
        $group3->get('/search/{keyword}', \App\Action\UserSearchAction::class);
        $group3->post('', \App\Action\UserCreateAction::class);
        //});
    })->add(JwtAuthMiddleware::class);

    // Docs - Swagger
    $app->get('/docs/v1', \App\Action\Docs\SwaggerUiAction::class);

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
