<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class UserReaderRepository
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
     * Get user by the given user id.
     *
     * @param int $userId The user id
     *
     * @throws DomainException
     *
     * @return UserData The user data
     */
    public function getUserById(int $userId): UserData
    {
        $sql = "SELECT USRID, USRNAME, USRFIRSTNAME, USRLASTNAME, USREMAIL, USRPROFILE FROM users WHERE USRID = :id;";
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $userId]);

        $row = $statement->fetch();

        if (!$row) {
            throw new DomainException(sprintf('User not found: %s', $userId));
        }

        // Map array to data object
        $user = new UserData();
        $user->id = (int)$row['USRID'];
        $user->username = (string)$row['USRNAME'];
        $user->firstName = (string)$row['USRFIRSTNAME'];
        $user->lastName = (string)$row['USRLASTNAME'];
        $user->email = (string)$row['USREMAIL'];
		$user->profile = (string)$row['USRPROFILE'];

        return $user;
    }
}
