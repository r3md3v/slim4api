<?php

namespace App\Middleware;

use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Middleware.
 */
final class HttpsMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var int
     */
    private $port;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param ResponseFactoryInterface $responseFactory The response factory
     * @param ContainerInterface $ci
     * @param $lf
     */
    public function __construct(ResponseFactoryInterface $responseFactory, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $ci;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
        $settings = $this->container->get('settings')['redirection'];
        $this->port = (int)$settings['port'];
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $uri = $request->getUri();

        if ($uri->getHost() !== 'localhost' && $uri->getScheme() !== 'https') {
            $this->logger->info("HttpsMiddleware: current uri: $uri, scheme: " . $uri->getScheme());
            $this->logger->debug("Host: " . $uri->getHost() . ",protocol: " . $uri->getScheme() . ",  url: $uri");
            $url = (string)$uri->withScheme('https')->withPort($this->port);

            $this->logger->info("HTTPS redirecting to $url");
            //unknown methods withRedirect for ResponseInterface
            //return $this->responseFactory->createResponse()->withRedirect($url);
            return $this->responseFactory->createResponse()->withHeader('Location', (string)$url)->withStatus(301);

        }

        return $handler->handle($request);
    }
}