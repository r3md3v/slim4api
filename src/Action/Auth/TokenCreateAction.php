<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Domain\Login\Service\LoginManager;
use App\Domain\Login\Service\TokenManager;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class TokenCreateAction
{
    private $jwtAuth;
    /**
     * @var bool
     */
    private $logtokens;
    /**
     * @var bool
     */
    private $loglogins;
    /**
     * @var LoginManager
     */
    private $loginManager;

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
     * @param LoginManager $loginManager The login manager
     * @param TokenManager $tokenManager The token manager
     * @param JwtAuth $jwtAuth The JWT authentifier
     * @param ContainerInterface $ci The container interface
     * @param LoggerFactory $lf The loggerFactory
     */
    public function __construct(LoginManager $loginManager, TokenManager $tokenManager, JwtAuth $jwtAuth, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->LoginManager = $loginManager;
        $this->tokenManager = $tokenManager;
        $this->logtokens = (bool)$ci->get('settings')['jwt']['logtokens'];
        $this->loglogins = (bool)$ci->get('settings')['jwt']['loglogins'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        $username = (string)($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        // Feed the logger
        $this->logger->debug("TokenCreateAction: username: {$username}");

        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Build the HTTP response
        $response = $response
            ->withHeader('Content-Type', 'application/json');

        // Check for valid authentication credentials
        if ('ok' != $this->LoginManager->getLoginDetails($username, $password, $this->loglogins)->status) {
            $this->logger->debug('TokenCreateAction: invalid login/password');
            $response->getBody()->write(json_encode('Unauthorized access'));

            return $response
                ->withStatus(401, 'Unauthorized');
        }

        // Check for valid existing tokefor user
        // if (-1 == $this->LoginManager->getTokenForUser($username))

        // Create a fresh token
        $token = $this->jwtAuth->createJwt([
            'uid' => $username,
        ]);

        $lifetime = $this->jwtAuth->getLifetime();

        // Save token into db if not exist
        if ($this->logtokens) {
            $this->tokenManager->logTokenDetails($username, $token, $lifetime);
        }

        // Transform the result into a OAuh 2.0 Access Token Response
        // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
        $result = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $lifetime,
        ];

        // Build the HTTP response
        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', $result['access_token'])
            // add cookie
            ->withHeader(
                'Set-Cookie',
                'Authorization=' . $result['access_token'] . '; HttpOnly; Secure; Path=/; SameSite=Strict; Max-Age=' . $lifetime
            );

        // Feed the logger
        $this->logger->debug("TokenCreateAction: JWT created for [{$username}]");

        $response->getBody()->write((string) json_encode($result));

        return $response->withStatus(201);
    }
}
