<?php

namespace App\Domain\User\Repository;

use DomainException;
use PDO;

/**
 * Repository.
 */
class UserDeletorRepository
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
     * Delete user by the given user id.
     *
     * @param int $userId The user id
     *
     * @throws DomainException
     *
     * @return int $userId
     */
    public function deleteUserById(int $userId)
    {
        $sql = 'DELETE FROM users WHERE USRID = :id;';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $userId]);

        if (0 == $statement->rowCount()) {
            throw new DomainException(sprintf('User not found: %s', $userId));
        }

        return (int) $userId;
    }
}
