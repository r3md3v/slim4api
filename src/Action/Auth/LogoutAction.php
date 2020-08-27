<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LogoutAction
{
    private $jwtAuth;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(JwtAuth $jwtAuth, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface
    {
        //$token = explode(' ', (string) $request->getHeaderLine('Authorization'))[1] ?? ''; // not working
        $token = isset($_COOKIE['Authorization']) ? $_COOKIE['Authorization'] : -1; // must be a way toread cookie via SLIM...

        // Result
        if (-1 == $token) {
            $result = [
                'message' => 'No JWT active',
                'token_type' => 'Bearer',
                'access_token' => $token,
            ];
        } else {
            $result = [
                'message' => 'Active JWT canceled',
                'token_type' => 'Bearer',
                'access_token' => $token,
            ];
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

        $response->getBody()->write((string)json_encode($result));

        return $response->withStatus(201);
    }
}
