<?php

namespace App\Middleware;

use App\Auth\JwtAuth;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * JWT Auth middleware.
 */
final class JwtAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var JwtAuth
     */
    private $jwtAuth;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param JwtAuth                  $jwtAuth         The JWT auth
     * @param LoggerFactory            $lf              The logger factory
     * @param ResponseFactoryInterface $responseFactory The response factory
     */
    public function __construct(
        JwtAuth $jwtAuth,
        ResponseFactoryInterface $responseFactory,
        LoggerFactory $lf
    ) {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
        $this->logger = $lf
            ->addFileHandler('error.log')
            ->addConsoleHandler()
            ->createInstance('error');
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $token = explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? '';

        $this->logger->debug("token header: " . $token);
        if (!$token) {
            $token = isset($_COOKIE["Authorization"]) ? $_COOKIE["Authorization"] : '';
            $this->logger->debug("token cookie: " . $token);
        }

        if (!$token || !$this->jwtAuth->validateToken($token)) {
            $response = $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');

            $response->getBody()->write(json_encode('Unauthorized'));
            return $response;
        }

        return $handler->handle($request);
    }
}