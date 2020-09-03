<?php

namespace App\Middleware;

use App\Auth\JwtAuth;
use App\Domain\Login\Service\TokenManager;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * JWT Claim middleware.
 */
final class JwtClaimMiddleware implements MiddlewareInterface
{
    /**
     * @var JwtAuth
     */
    private $jwtAuth;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param JwtAuth       $jwtAuth      The JWT auth
     * @param TokenManager  $tokenManager The token manager
     * @param LoggerFactory $lf           The logger factory
     */
    public function __construct(JwtAuth $jwtAuth, TokenManager $tokenManager, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->tokenManager = $tokenManager;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
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
        $authorization = (string) $request->getHeaderLine('Authorization');

        $authorizations = explode(' ', $authorization);
        $type = $authorizations[0] ?? '';
        $credentials = $authorizations[1] ?? '';

        $tokenDetails = $this->tokenManager->getTokenDetails($authorization);

        if ('Bearer' === $type && $this->jwtAuth->validateToken($credentials) && '0' != $tokenDetails->status) {
            // Append valid token
            $parsedToken = $this->jwtAuth->createParsedToken($credentials);
            $request = $request->withAttribute('token', $parsedToken);

            // Append the user id as request attribute
            $request = $request->withAttribute('uid', $parsedToken->getClaim('uid'));

            // Add more claim values as attribute...
            //$request = $request->withAttribute('locale', $parsedToken->getClaim('locale'));
        }

        return $handler->handle($request);
    }
}
