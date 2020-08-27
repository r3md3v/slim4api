<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Domain\Login\Service\TokenManager;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class LogoutAction
{
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
     * Constructor.
     *
     * @param TokenManager  $tokenManager The token manager
     * @param JwtAuth       $jwtAuth      The JWT authentifier
     * @param LoggerFactory $lf           The loggerFactory
     */
    public function __construct(TokenManager $tokenManager, JwtAuth $jwtAuth, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->tokenManager = $tokenManager;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        //$token = explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? ''; // if header is used
        $token = isset($_COOKIE['Authorization']) ? $_COOKIE['Authorization'] : -1; // must be a way to read cookie via SLIM...

        // Mark token as revoked
        $tokenr = $this->tokenManager->revokeToken($token);

        // Transform the result into the JSON representation
        if (-1 == $token) {
            $result = [
                'message' => 'No JWT active',
                'token_type' => 'Bearer',
                'access_token' => $token,
            ];
        } else {
            if (0 != $tokenr) {
                $result = [
                    'message' => 'Active JWT canceled and revoked',
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ];
            } else {
                $result = [
                    'message' => 'Active JWT canceled but NOT foune/revoked',
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ];
            }
        }

        // Build the HTTP response
        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', $result['access_token'])
            // add cookie
            ->withHeader(
                'Set-Cookie',
                'Authorization=; HttpOnly; Secure; Path=/;  SameSite=Strict; Max-Age=0'
            );

        // Feed the logger
        $this->logger->debug('LogoutAction: Active JWT canceled'); // for [{$username}]

        $response->getBody()->write((string) json_encode($result));

        return $response->withStatus(201);
    }
}
