<?php

namespace App\Action;

use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class HomeAction
{
    /**
     * @var Responder
     */
    //private $responder;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $build;

    /**
     * @ContainerInterface container
     */
    private $container;

    /**
     * @var the Logger
     */
    private $logger;
    /**
     * @var int
     */
    private $timestamp;
    /**
     * @var false|string
     */
    private $datetime;

    /**
     * The constructor.
     *
     * @param ContainerInterface $ci The Container
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->logger = $lf
            ->addFileHandler('error.log')
            ->addConsoleHandler()
            ->createInstance('error');
        //get containers objects
        $this->container = $ci;
        $apiSettings = $this->container->get('settings')['api'];
        //populate attributes
        $this->name = $apiSettings['name'];
        $this->version = $apiSettings['version'];
        $this->url = $apiSettings['url'];
        $this->build = $apiSettings['build'];
        $this->datetime = date('Y-m-d H:i:s') . ' ' . date_default_timezone_get();
        $this->timestamp = time();
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        //$response->getBody()->write((string)json_encode(['success' => true]));
        $this->logger->info('HomeAction: get infos');

        $message = [
            'name' => $this->name,
            'version' => $this->version,
            'url' => $this->url,
            'build' => $this->build,
            'datetime' => $this->datetime,
            'timestamp' => $this->timestamp,
        ];

        //$response->getBody()->write((string)json_encode(['success' => true]));
        $response->getBody()->write((string)json_encode($message));

        return $response->withHeader('Content-Type', 'application/json');
    }
}