<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class UserListerRepository
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
     * Get user list
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @return users List of Users
     * @throws DomainException
     *
     */
    public function getUsers($page = 1, $pagesize = 50): array
    {

        $usernb = $this->countUsers();

        if ($usernb == 0)
            throw new DomainException(sprintf('No user!'));

        $pagemax = ceil($usernb / $pagesize);
        $limit = (--$page) * $pagesize;

        $sql = "SELECT USRID, USRNAME, USRFIRSTNAME, USRLASTNAME, USREMAIL, USRPROFILE FROM users LIMIT ?, ?;";
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $users = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $user = new UserData();
            $user->id = (int)$row['USRID'];
            $user->username = (string)$row['USRNAME'];
            $user->firstName = (string)$row['USRFIRSTNAME'];
            $user->lastName = (string)$row['USRLASTNAME'];
            $user->email = (string)$row['USREMAIL'];
            $user->profile = (string)$row['USRPROFILE'];
            array_push($users, $user);
        }

        if (count($users) == 0) {
            throw new DomainException(sprintf('No user page #%d!', ($page + 1)));
        }

        return $users;
    }

    public function countUsers(): int
    {
        $sql = "SELECT COUNT(*) AS nb FROM users;";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['nb'];
    }
}