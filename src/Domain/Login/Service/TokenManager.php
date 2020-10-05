<?php

namespace App\Action\Auth;

namespace App\Domain\Login\Service;

use App\Domain\Login\Data\TokenData;
use App\Domain\Login\Repository\TokenRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var mixed
     */
    private $tokenlifetime;

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
        // Get first chunk of JWT (not decoded)
        $chunk = $this->decodeTokenHeader($token, 1);

        return $this->repository->revokeTokenByJwt($chunk);
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
        // Get first chunk of JWT (not decoded)
        $chunk = $this->decodeTokenHeader($token, 1);

        return $this->repository->insertTokenDetails($username, $chunk, $lifetime);
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

    /**
     * Decode token header.
     *
     * @param string $token The Token
     * @param bool   $raw   Decode or not
     *
     * @return string decoded token
     */
    public function decodeTokenHeader($token, $raw = 1): string
    {
        // Split token in 3
        $splitJwt = explode('.', $token);
        if (3 != count($splitJwt)) {
            return -1;
        }
        $chunk = $splitJwt[0];
        if ($remainder = strlen($chunk) % 4) {
            $chunk .= str_repeat('=', 4 - $remainder);
        }

        // Return raw first chunk or decoded values
        if ($raw) {
            return $chunk;
        }

        return base64_decode(strtr($chunk, '-_', '+/'));
    }
}
