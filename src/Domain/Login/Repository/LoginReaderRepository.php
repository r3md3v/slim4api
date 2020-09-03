<?php

namespace App\Domain\Login\Repository;

use App\Domain\Login\Data\LoginData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class LoginReaderRepository
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
        $sql = 'SELECT JWUID, JWUUSERNAME, JWUPASSWORD, JWUEMAIL, JWULASTTOKEN, JWUSTATUS FROM logins WHERE JWUUSERNAME = :username OR JWUEMAIL = :username LIMIT 1';
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
            $login = new LoginData();
            $login->id = (int) 0;
            $login->username = (string) $username;
            $login->email = (string) 'na';
            $login->token = (string) 'na';
            $login->status = (string) $loginresult;
        } else {
            // Map array to data object
            $login = new LoginData();
            $login->id = (int) $row['JWUID'];
            $login->username = (string) $row['JWUUSERNAME'];
            $login->email = (string) $row['JWUEMAIL'];
            $login->token = (string) $row['JWULASTTOKEN'];
            $login->status = (string) $row['JWUSTATUS'];
        }

        return $login;
    }

    /**
     * Save login attempt.
     *
     * @param string $username The login username
     * @param string $sourceip The Source IP
     * @param object $login    The result
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
}
