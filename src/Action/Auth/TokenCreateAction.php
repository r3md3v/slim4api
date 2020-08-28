<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Domain\Login\Service\LoginReader;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class TokenCreateAction
{
    private $jwtAuth;

    /**
     * @var LoginReader
     */
    private $loginReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param LoginReader   $loginReader The login Reader
     * @param JwtAuth       $jwtAuth     The JWT authentifier
     * @param LoggerFactory $lf          The loggerFactory
     */
    public function __construct(LoginReader $loginReader, JwtAuth $jwtAuth, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->loginReader = $loginReader;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = (array) $request->getParsedBody();

        $username = (string) ($data['username'] ?? '');
        $password = (string) ($data['password'] ?? '');

        // Feed the logger
        $this->logger->debug("TokenCreateAction: username: {$username}");

        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Validate login (pseudo code)
        //$isValidLogin = ('user' === $username && 'secret' === $password);

        // $userAuthData = $this->userAuth->authenticate($username, $password);
        $login = $this->loginReader->getLoginDetails($username, $password);

        /*if (!$isValidLogin) {
            $this->logger->debug('TokenCreateAction: invalid login/password');
            // Invalid authentication credentials
            $response->getBody()->write(json_encode('Unauthorized access'));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized')
            ;
        }*/

        // Create a fresh token
        $token = $this->jwtAuth->createJwt([
            'uid' => $username,
        ]);

        $lifetime = $this->jwtAuth->getLifetime();

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
                'Authorization='.$result['access_token'].'; HttpOnly; Secure; Path=/; SameSite=Strict; Max-Age='.$lifetime
            )
        ;

        // Feed the logger
        $this->logger->debug("TokenCreateAction: JWT created for [{$username}]");

        $response->getBody()->write((string) json_encode($result));

        return $response->withStatus(201);
    }
}
