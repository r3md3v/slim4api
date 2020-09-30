<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use App\Factory\LoggerFactory;
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
     * @param PDO           $connection The database connection
     * @param LoggerFactory $lf         The logger Factory
     */
    public function __construct(PDO $connection, LoggerFactory $lf)
    {
        $this->connection = $connection;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Get user list.
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @throws DomainException
     *
     * @return users List of Users
     */
    public function getUsers(int $page = 1, int $pagesize = 50): array
    {
        // Feed the logger
        $this->logger->debug("UserListerRepository.getUsers: page: {$page}, size: {$pagesize}");

        $usernb = $this->countUsers();

        if (0 == $usernb) {
            throw new DomainException(sprintf('No user!'));
        }
        $pagemax = ceil($usernb / $pagesize);
        $pagestart = (--$page) * $pagesize;

        $sql = 'SELECT USRID, USRNAME, USRFIRSTNAME, USRLASTNAME, USREMAIL, USRPROFILE FROM users LIMIT ?, ?;';
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $pagestart, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $users = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $user = new UserData(
                (int) $row['USRID'],
                (string) $row['USRNAME'],
                (string) $row['USRFIRSTNAME'],
                (string) $row['USRLASTNAME'],
                (string) $row['USREMAIL'],
                (string) $row['USRPROFILE']
            );
            array_push($users, $user);
        }

        if (0 == count($users)) {
            throw new DomainException(sprintf('No user page %d / %d', $page + 1, $pagemax));
        }

        return $users;
    }

    /**
     * Get user count.
     *
     * @return nb Nb of Users
     */
    public function countUsers(): int
    {
        $sql = 'SELECT COUNT(*) AS nb FROM users;';
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row['nb'];
    }
}
