<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Domain\Login\Service\LoginReader;
use App\Domain\Login\Service\TokenManager;
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
     * @param LoginReader   $loginReader  The login reader
     * @param TokenManager  $tokenManager The token manager
     * @param JwtAuth       $jwtAuth      The JWT authentifier
     * @param LoggerFactory $lf           The loggerFactory
     */
    public function __construct(LoginReader $loginReader, TokenManager $tokenManager, JwtAuth $jwtAuth, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->loginReader = $loginReader;
        $this->tokenManager = $tokenManager;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $data = (array) $request->getParsedBody();

        $username = (string) ($data['username'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $sourceip = (string) $this->loginReader->getUserIpAddr();

        // Feed the logger
        $this->logger->debug("TokenCreateAction: username: {$username}");

        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Build the HTTP response
        $response = $response
            ->withHeader('Content-Type', 'application/json')
        ;

        // Validate login (pseudo code) $isValidLogin = ('user' === $username && 'secret' === $password)

        //$login = $this->loginReader->getLoginDetails($username, $password, $sourceip)->status

        // Check for valid authentication credentials
        if ('ok' != $this->loginReader->getLoginDetails($username, $password, $sourceip)->status) {
            $this->logger->debug('TokenCreateAction: invalid login/password');
            $response->getBody()->write(json_encode('Unauthorized access'));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');
        }

        /* Check for valid existing token for user
        if (-1 == $this->loginReader->getTokenForUser($username)) {
            $this->logger->debug('TokenCreateAction: token locked');
            $response->getBody()->write(json_encode('Token locked'));

            return $response
                ->withStatus(401, 'Unauthorized')
            ;
        }*/

        // Create a fresh token
        $token = $this->jwtAuth->createJwt([
            'uid' => $username,
        ]);

        $lifetime = $this->jwtAuth->getLifetime();

        //$tokenv = $this->jwtAuth->validateToken($token); // of course
        //$tokend = $this->jwtAuth->decodeToken($token);
        //get existingtoken ?
        //echo "jwt={$jwt} tokenv={$tokenv} tokend=".$tokend->getHeader('jti').'<br />';

        // Save token into db if not exist
        $this->tokenManager->logTokenDetails($username, $token, $lifetime);

        // Cleanup token log
        // $this->loginReader->logTokenCleanup();

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