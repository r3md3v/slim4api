<?php

use App\Auth\JwtAuth;
use App\Domain\Login\Service\Tools;
use App\Factory\LoggerFactory;
use App\Middleware\TrailingSlashMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;

// redir http to https

return [
    'settings' => function () {
        return require __DIR__.'/settings.php';
    },

    Tools::class  =>function(){
        return new Tools();
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        // https://odan.github.io/2020/04/07/slim4-https-middleware.html
        // https://odan.github.io/2019/12/02/slim4-oauth2-jwt.html
        return $container->get(App::class)->getResponseFactory();
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    // oauth2 JWT https://odan.github.io/2019/12/02/slim4-oauth2-jwt.html
    JwtAuth::class => function (ContainerInterface $container) {
        $config = $container->get('settings')['jwt'];

        $issuer = (string) $config['issuer'];
        $lifetime = (int) $config['lifetime'];
        $privateKey = (string) $config['private_key'];
        $publicKey = (string) $config['public_key'];

        return new JwtAuth($issuer, $lifetime, $privateKey, $publicKey);
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get('settings')['logger']);
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['logger'];
        $loggerFactory = $container->get(LoggerFactory::class);
        $logger = $loggerFactory->addFileHandler('error.log')->createInstance('error');

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool) $settings['display_error_details'],
            (bool) $settings['log_errors'],
            (bool) $settings['log_error_details'],
            $logger
        );
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $driver = $settings['driver'];
        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "{$driver}:host={$host};dbname={$dbname};charset={$charset}";

        return new PDO($dsn, $username, $password, $flags);
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    Twig::class => function (ContainerInterface $container) {
        $twigSettings = $container->get('settings')['twig'];

        $loader = new FilesystemLoader($twigSettings['path_templates']);
        $options = ['cache' => $twigSettings['path_cache']];

        return  new Twig($loader, $options);
    },

    TrailingSlashMiddleware::class => function (ContainerInterface $container) {
        $trailingSetting = $container->get('settings')['trail'];

        return new TrailingSlashMiddleware((bool) $trailingSetting);
    },
];
