<?php

namespace App\Domain\Login\Repository;

use App\Domain\Login\Data\TokenData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class TokenRepository
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get Token details.
     *
     * @param string $token The token
     *
     * @throws DomainException
     *
     * @return TokenData The token data
     */
    public function getTokenByJwt(string $token): TokenData
    {
        $sql = 'SELECT * FROM logtokens WHERE BINARY TOKTOKEN = :token ORDER BY TOKISSUEDAT DESC LIMIT 1;'; // BINARY compare for exact match
        $statement = $this->connection->prepare($sql);

        $token = str_replace('Bearer ', '', $token);

        $statement->bindParam('token', $token);
        $statement->execute();

        $row = $statement->fetch();

        if (!$row) {
            $tokens = explode('.', $token);
            //throw new DomainException(sprintf('Token not found: %s', $tokens[0])); // only show header of token for security reason

            $token = new TokenData(
                (int) 0,
                (string) 'No active token',
                (string) $tokens[0],
                (string) 1,
                (string) 'na',
                (string) 'na',
            );
        } else {
            // Map array to data object
            $token = new TokenData(
                (int) $row['TOKID'],
                (string) $row['TOKUSERNAME'],
                (string) $row['TOKTOKEN'],
                (string) $row['TOKSTATUS'],
                (string) $row['TOKISSUEDAT'],
                (string) $row['TOKEXPIREDAT'],
            );
        }

        return $token;
    }

    /**
     * Save token.
     *
     * @param string $username The login username
     * @param string $token    The Token
     * @param string $lifetime The lifetime
     *
     * @return tokenid The token id
     */
    public function insertTokenDetails(string $username, string $token, string $lifetime): int
    {
        $paramSql = [
            'username' => $username,
            'token' => $token,
            'status' => '1',
        ];

        $sql = 'INSERT INTO logtokens SET 
            TOKUSERNAME=:username, 
            TOKTOKEN=:token,
            TOKSTATUS=:status,
            TOKISSUEDAT=NOW(),
            TOKEXPIREDAT= DATE_ADD(NOW(), INTERVAL '.$lifetime.' SECOND);';

        $this->connection->prepare($sql)->execute($paramSql);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Revoke token.
     *
     * @param string $token The Token
     *
     * @throws DomainException
     *
     * @return nbrow The nb of rows
     */
    public function revokeTokenByJwt(string $token): int
    {
        $sql = 'UPDATE logtokens SET TOKSTATUS = 0 WHERE TOKTOKEN = :token';
        $statement = $this->connection->prepare($sql);
        $statement->bindParam('token', $token);

        $statement->execute();

        $nbrows = $statement->rowCount();

        if (0 == !$nbrows) {
            // Show only header of token for security reason
            $tokens = explode('.', $token);
            // Remove this warning to show only "Unauthorized"
            throw new DomainException(sprintf('Token not found: %s', $tokens[0]));
        }

        return (int) $nbrows;
    }

    /**
     * Cleanup token log.
     *
     * @return nbrow The nb of rows
     */
    public function deleteExpiredTokens(): int
    {
        $sql = 'DELETE FROM logtokens WHERE TOKEXPIREDAT <= NOW()';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        return (int) $statement->rowCount();
    }

    /**
     * Get token log count.
     *
     * @return nb Nb of tokens
     */
    public function countTokens(): int
    {
        $sql = 'SELECT COUNT(*) AS nb FROM logtokens;';
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) $row['nb'];
    }
}
