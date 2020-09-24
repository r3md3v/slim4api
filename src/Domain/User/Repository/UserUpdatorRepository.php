<?php

namespace App\Domain\User\Repository;

use PDO;

/**
 * Repository.
 */
class UserUpdatorRepository
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
     * Update user row.
     *
     * @param int   $userId The user id
     * @param array $user   The user data
     *
     * @return bool row updated
     */
    public function updatetUser(int $userId, array $user): bool
    {
        $paramSql = [
            'username' => $user['username'],
            'password' => $user['password'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'profile' => $user['profile'],
            'email' => $user['email'],
            'id' => $userId,
        ];

        $sql = "UPDATE users SET 
                USRNAME=:username, 
                USRPASS=:password,
                USRFIRSTNAME=:first_name, 
                USRLASTNAME=:last_name, 
                USREMAIL=:email,
                USRPROFILE=:profile, 
                USRUPDATEDBY='slim4api',
                USRUPDATEDAT=now()
                WHERE USRID=:id;";

        $statement = $this->connection->prepare($sql);
        $statement->execute($paramSql);

        return $statement->rowCount() > 0;
    }

    /**
     * check if userid or email is already in database for other id.
     *
     * @param userId
     * @param username
     * @param email
     *
     * @return bool rowCount exists
     */
    public function userExists(int $userId, string $username, string $email): bool
    {
        $params = [];
        $params['username'] = $username;
        $params['email'] = $email;
        $params['id'] = $userId;

        $sql = 'SELECT * FROM users AS u 
                WHERE (u.USRNAME =:username OR u.USREMAIL =:email)
                AND u.USRID <>:id;';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }
}
