<?php

namespace App\Domain\User\Repository;

use PDO;

/**
 * Repository.
 */
class UserCreatorRepository
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
     * Insert user row.
     *
     * @param array $user The user
     *
     * @return int The new ID
     */
    public function insertUser(array $user): int
    {

        $paramSql = [
            'username' => $user['username'],
            'password' => $user['password'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'profile' => $user['profile'],
            'email' => $user['email'],
        ];

        $sql = "INSERT INTO users SET 
                USRNAME=:username, 
                USRPASS=:password,
                USRFIRSTNAME=:first_name, 
                USRLASTNAME=:last_name, 
                USREMAIL=:email,
                USRPROFILE=:profile, 
                USRUPDATEDBY='slim4api',
                USRUPDATEDAT=now();";

        $this->connection->prepare($sql)->execute($paramSql);

        return (int)$this->connection->lastInsertId();
    }


    /**
     * check if userid or email is already in database.
     *
     * @param username
     * @param email
     *
     * @return boolean
     */
    public function userExists(string $username, string $email): bool
    {

        $params = [];
		$params['username'] = $username;
        $params['email'] = $email;

        $sql = "SELECT * FROM users AS u 
                WHERE u.USRNAME =:username OR u.USREMAIL =:email;";

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }
}
