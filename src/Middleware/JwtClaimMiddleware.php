<?php

namespace App\Middleware;

use App\Auth\JwtAuth;
use Psr\Container\ContainerInterface;
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
     * The constructor.
     *
     * @param JwtAuth $jwtAuth The JWT auth
     */
    public function __construct(JwtAuth $jwtAuth, ContainerInterface $ci)
    {
        $this->jwtAuth = $jwtAuth;
        //$this->logger = $ci
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
    ): ResponseInterface {
        $authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
        $type = $authorization[0] ?? '';
        $credentials = $authorization[1] ?? '';

        if ($type === 'Bearer' && $this->jwtAuth->validateToken($credentials)) {
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