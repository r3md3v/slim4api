<?php

namespace App\Action;

use App\Domain\Customer\Service\CustomerLister;
use App\Domain\Login\Service\LoginLister;
use App\Domain\User\Service\UserLister;
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
     * @var int
     */
    private $timestamp;

    /**
     * @var false|string
     */
    private $datetime;

    /**
     * @var UserLister
     */
    private $userLister;

    /**
     * @var CustomerLister
     */
    private $customerLister;

    /**
     * @var LoginLister
     */
    private $loginLister;

    /**
     * @ContainerInterface container
     */
    private $container;

    /**
     * @var the Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserLister         $userLister     The user lister
     * @param CustomerLister     $customerLister The customer lister
     * @param LoginLister        $loginLister    The login lister
     * @param ContainerInterface $ci             The Container
     * @param LoggerFactory      $lf             The loggerFactory
     */
    public function __construct(UserLister $userLister, CustomerLister $customerLister, LoginLister $loginLister, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->userLister = $userLister;
        $this->customerLister = $customerLister;
        $this->loginLister = $loginLister;

        //set logger
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');

        //get containers objects
        $this->container = $ci;
        $apiSettings = $this->container->get('settings')['api'];
        //populate attributes
        $this->name = $apiSettings['name'];
        $this->version = $apiSettings['version'];
        $this->url = $apiSettings['url'];
        $this->build = $apiSettings['build'];
        $this->datetime = date('Y-m-d H:i:s').' '.date_default_timezone_get();
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

        $tables['users'] = $this->userLister->getUserCount();
        $tables['customers'] = $this->customerLister->getCustomerCount();
        $tables['logins'] = $this->loginLister->getLoginCount();

        $message = [
            'name' => $this->name,
            'version' => $this->version,
            'url' => $this->url,
            'build' => $this->build,
            'datetime' => $this->datetime,
            'timestamp' => $this->timestamp,
            'tables' => $tables,
        ];

        //$response->getBody()->write((string)json_encode(['success' => true]));
        $response->getBody()->write((string) json_encode($message));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
