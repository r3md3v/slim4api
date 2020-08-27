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
     * Get a login according to username+password.
     *
     * @param string $username The login username
     * @param string $password The login password
     *
     * @throws DomainException
     *
     * @return LoginData The login data
     */
    public function getLoginByUP(string $username, string $password): LoginData
    {
        $sql = 'SELECT JWUID, JWUUSERNAME, JWUEMAIL, JWULASTTOKEN, JWUSTATUS FROM usersjwt WHERE JWUUSERNAME = :username AND JWUPASSWORD = :password;';
        $statement = $this->connection->prepare($sql);

        $username = htmlspecialchars(strip_tags($username));
        $password = password_hash(htmlspecialchars(strip_tags($password, PASSWORD_BCRYPT)));

        $statement->bindParam('username', $username);
        $statement->bindParam('password', $password);

        $statement->execute();

        $row = $statement->fetch();

        if (!$row) {
            throw new DomainException(sprintf('Login incorrect for: %s', $username));
        }

        if (1 != $row['JWUSTATUS']) {
            throw new DomainException(sprintf('User not active: %s', $username));
        }

        // Map array to data object
        $login = new LoginData();
        $login->id = (int) $row['JWUID'];
        $login->username = (string) $row['JWUNAME'];
        $login->email = (string) $row['JWUEMAIL'];
        $login->token = (string) $row['JWUTOKEN'];
        $login->status = (string) $row['JWUSTATUS'];

        return $login;
    }
}
