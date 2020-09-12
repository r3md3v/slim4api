<?php

namespace App\Action\Auth;

namespace App\Domain\Login\Service;

use App\Domain\Login\Data\TokenData;
use App\Domain\Login\Repository\TokenRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class TokenManager
{
    /**
     * @var TokenRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param TokenRepository    $repository The repository
     * @param ContainerInterface $ci         The container interface
     * @param LoggerFactory      $lf         The logger Factory
     */
    public function __construct(TokenRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->tokenlifetime = $ci->get('settings')['jwt']['lifetime'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read a token by the given jwt.
     *
     * @param string $token The token
     *
     * @throws ValidationException
     *
     * @return TokenData The token data
     */
    public function getTokenDetails(string $token): TokenData
    {
        return $this->repository->getTokenByJwt($token);
    }

    /**
     * Revoke token by the given jwt.
     *
     * @param string $token The Token
     *
     * @throws ValidationException
     *
     * @return int result The result
     */
    public function revokeToken(string $token): int
    {
        return $this->repository->revokeTokenByJwt($token);
    }

    /**
     * Save token.
     *
     * @param string $username The login username or email
     * @param string $token    The Token
     * @param string $lifetime The lifetime
     *
     * @return int token The row id
     */
    public function logTokenDetails(string $username, string $token, string $lifetime)
    {
        /*// Decode first chunk of JWT
        $splitJwt = explode('.', $token);
        if (3 != count($splitJwt)) {
            return -1;
        }
        $chunk = $splitJwt[0];
        if ($remainder = strlen($chunk) % 4) {
            $chunk .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($chunk, '-_', '+/'));
        $this->repository->insertTokenDetails($username, $decoded, $lifetime);
        */
        return $this->repository->insertTokenDetails($username, $token, $lifetime);
    }

    /**
     * Cleanup token log.
     *
     * @return int row Nb of rows
     */
    public function cleanupTokens(): int
    {
        return $this->repository->deleteExpiredTokens();
    }

    /**
     * Count tokens.
     *
     * @return int nb
     */
    public function getTokenCount(): int
    {
        return $this->repository->countTokens();
    }
}
