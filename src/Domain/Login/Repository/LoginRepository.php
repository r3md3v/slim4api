<?php

namespace App\Domain\Login\Repository;

use App\Domain\Login\Data\LoginData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class LoginRepository
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * The constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get a login according to username or email + password.
     *
     * @param string $username The login username or email
     * @param string $password The login password
     *
     * @throws DomainException
     *
     * @return LoginData The login data
     */
    public function getLoginByUMP(string $username, string $password): LoginData
    {
        $sql = 'SELECT JWUID, JWUUSERNAME, JWUPASSWORD, JWUEMAIL, JWUDESCRIPTION, JWULASTTOKEN, JWUSTATUS FROM logins WHERE JWUUSERNAME = :username OR JWUEMAIL = :username LIMIT 1';
        $statement = $this->connection->prepare($sql);

        $username = htmlspecialchars(strip_tags($username));

        $statement->bindParam('username', $username);

        $statement->execute();

        $row = $statement->fetch();

        $loginresult = 'ok';
        if (!$row) {
            $loginresult = sprintf('Login incorrect for: %s', $username);
        } elseif (!password_verify($password, $row['JWUPASSWORD'])) {
            $loginresult = sprintf('Login incorrect for: %s', $username);
        } elseif (1 != $row['JWUSTATUS']) {
            $loginresult = sprintf('Access locked: %s', $username);
        }

        // Return fake result if login not ok
        if ('ok' != $loginresult) {
            $login = new LoginData(
                (int) 0,
                (string) $username,
                (string) 'na',
                (string) 'na',
                (string) 'na',
                (string) $loginresult
            );
        } else {
            // Map array to data object
            $login = new LoginData(
                (int) $row['JWUID'],
                (string) $row['JWUUSERNAME'],
                (string) $row['JWUEMAIL'],
                (string) $row['JWUDESCRIPTION'],
                (string) $row['JWULASTTOKEN'],
                (string) $row['JWUSTATUS']
            );
        }

        return $login;
    }

    /**
     * Get login list.
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     * @param mixed $page
     * @param mixed $pagesize
     *
     * @throws DomainException
     *
     * @return logins List of Logins
     */
    public function getLogins(int $page, int $pagesize): array
    {
        $loginnb = $this->countLogins();

        if (0 == $loginnb) {
            throw new DomainException(sprintf('No login!'));
        }
        $pagemax = ceil($loginnb / $pagesize);
        $limit = (--$page) * $pagesize;

        $sql = 'SELECT JWUID, JWUUSERNAME, JWUEMAIL, JWUDESCRIPTION, JWULASTTOKEN, JWUSTATUS FROM logins LIMIT ?, ?;';
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $logins = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $login = new LoginData(
                (int) $row['JWUID'],
                (string) $row['JWUUSERNAME'],
                (string) $row['JWUEMAIL'],
                (string) $row['JWUDESCRIPTION'],
                (string) $row['JWULASTTOKEN'],
                (string) $row['JWUSTATUS']
            );
            array_push($logins, $login);
        }

        if (0 == count($logins)) {
            throw new DomainException(sprintf('No login page %d / %d!', $page + 1, $pagemax));
        }

        return $logins;
    }

    /**
     * Save login attempt.
     *
     * @param string $username The login username
     * @param string $sourceip The Source IP
     * @param object $login    The result
     *
     * @return loginid Loginlog id
     */
    public function logLogin(string $username, string $sourceip, object $login): int
    {
        $paramSql = [
            'username' => $username,
            'sourceip' => $sourceip,
            'loginresult' => $login->status,
        ];

        $sql = 'INSERT INTO loglogins SET 
            LOGUSERNAME=:username, 
            LOGSOURCEIP=:sourceip,
            LOGRESULT=:loginresult,
            LOGUPDATEDAT = now();';

        $this->connection->prepare($sql)->execute($paramSql);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Cleanup login log.
     *
     * @param mixed $retention
     *
     * @return nbrow nb rows deleted
     */
    public function cleanupLogin($retention): int
    {
        $sql = 'DELETE FROM loglogins WHERE LOGUPDATEDAT < DATE_SUB(NOW(), INTERVAL '.$retention.' SECOND);';

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        return (int) $statement->rowCount();
    }

    /**
     * Get login count.
     *
     * @return nb Nb of logins
     */
    public function countLogins(): int
    {
        $sql = 'SELECT COUNT(*) AS nb FROM logins;';
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) $row['nb'];
    }
}
