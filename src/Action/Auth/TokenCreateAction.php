<?php

namespace App\Action\Auth;

use App\Auth\JwtAuth;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use DomainException;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

// check token
// https://jwt.io/?value=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjMxMzJlYmE0LTdjMjAtNDgxNC04NWMzLTYzOWNlODBmYWE2YyJ9.eyJpc3MiOiJ3d3cuZXhhbXBsZS5jb20iLCJqdGkiOiIzMTMyZWJhNC03YzIwLTQ4MTQtODVjMy02MzljZTgwZmFhNmMiLCJpYXQiOjE1OTc1NjU5MDcsIm5iZiI6MTU5NzU2NTkwNywiZXhwIjoxNTk3NTgwMzA3LCJ1aWQiOiJ1c2VyIn0.J-1SXt1q2R48WHLOaZJW83oRdIRmE0bCVURrwZRr5n1c4JqXHqpI1mykXA4Lcc1ITDiC8IvhPvpMr3mK5KYgRFU7TX8Tv9Vn-G5RJ9mCZfIUlMzjTn-eddLTPWKseDRKH6j2QkeHd4oXKgZeZCRPpPCmcwC8BvDFE02-j3zc1pWtXWLM-Vlw6VCUA2_pSwRb5sQTjvz5GHmakZsck3U99V5_gTsD7H4eEJ3B0p-zbQW2bApqrrfJAdlu7RSz2LO4civoRWE22kIdfBmktsEserL8RBgdn5AIw40yNh_VUCd5eNfAfs-J-1tek5-4r0a4n2VQycOFcju3Mz7JqEaMcw

final class TokenCreateAction
{
    private $jwtAuth;

    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(JwtAuth $jwtAuth, PDO $connection, LoggerFactory $lf)
    {
        $this->jwtAuth = $jwtAuth;
        $this->connection = $connection;
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

        // Validate login (pseudo code)
        // Warning: This should be done in an application service and not here!
        // $userAuthData = $this->userAuth->authenticate($username, $password);

        //
        // Here for testing purpose /////////////// START
        //
        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        $sql = 'SELECT JWUID, JWUUSERNAME, JWUPASSWORD, JWUEMAIL, JWULASTTOKEN, JWUSTATUS FROM usersjwt WHERE JWUUSERNAME = :username;';
        $statement = $this->connection->prepare($sql);

        $username = htmlspecialchars(strip_tags($username));
        //$password = password_hash(htmlspecialchars(strip_tags($password)), PASSWORD_BCRYPT);

        $statement->bindParam('username', $username);
        //$statement->bindParam('password', $password);

        $statement->execute();

        $row = $statement->fetch();
        $isValidLogin = true;
        if (!$row) {
            throw new DomainException(sprintf('Login incorrect for: %s', $username)); // no such user
            $isValidLogin = false;
        }
        if (1 != $row['JWUSTATUS']) {
            throw new DomainException(sprintf('User locked: %s', $username)); // user locked
            $isValidLogin = false;
        }
        if (!password_verify($password, $row['JWUPASSWORD'])) {
            throw new DomainException(sprintf('Login incorrect for: %s', $username)); // wrong pw
            $isValidLogin = false;
        }

        //
        // Here for testing purpose /////////////////////// END
        // $userAuthData = \App\Domain\Login\Service\LoginReader\getLoginDetails($username, $password);
        //
        //$isValidLogin = ('user' === $username && 'secret' === $password);

        if (!$isValidLogin) {
            $this->logger->debug('TokenCreateAction: invalid login/password');
            // Invalid authentication credentials
            $response->getBody()->write(json_encode('Unauthorized access'));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401, 'Unauthorized');
        }

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
                'Authorization=' . $result['access_token'] . '; HttpOnly; Secure; Path=/; SameSite=Strict; Max-Age=' . $lifetime
            );

        // Feed the logger
        $this->logger->debug("TokenCreateAction: JWT created for [{$username}]");

        $response->getBody()->write((string)json_encode($result));

        return $response->withStatus(201);
    }
}