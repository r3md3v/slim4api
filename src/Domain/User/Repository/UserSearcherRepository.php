<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use App\Factory\LoggerFactory;
use DomainException;
use PDO;

/**
 * Repository.
 */
class UserSearcherRepository
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
     * Get user search.
     *
     * @param string $keyword  Word to search
     * @param array  $in       Field exact name/human name
     * @param int    $page     page number
     * @param int    $pagesize page size
     *
     * @throws DomainException
     *
     * @return users Search of Users
     */
    public function getUsers(string $keyword, array $in, int $page, int $pagesize): array
    {
        $usernb = $this->countUsers();

        if (0 == $usernb) {
            $this->logger->info('UserSearcherRepository.getUsers: no user in table');

            throw new DomainException(sprintf('No user!'));
        }
        $pagemax = ceil($usernb / $pagesize);
        $pagestart = (--$page) * $pagesize;

        if (empty($in)) {
            $sql = 'SELECT USRID, USRNAME, USRFIRSTNAME, USRLASTNAME, USREMAIL, USRPROFILE FROM users WHERE USRNAME LIKE :keyword OR USRFIRSTNAME LIKE :keyword OR USRLASTNAME LIKE :keyword OR USREMAIL LIKE :keyword OR USRPROFILE LIKE :keyword LIMIT :pagestart, :pagesize ;';
        } else {
            $sql = "SELECT USRID, USRNAME, USRFIRSTNAME, USRLASTNAME, USREMAIL, USRPROFILE FROM users WHERE {$in[1]} LIKE :keyword LIMIT :pagestart, :pagesize ;";
        }

        // Feed the logger
        if (empty($in)) {
            $this->logger->debug("UserSearcherRepository.getUsers: keyword: {$keyword}, in: any, page: {$page}, size: {$pagesize},pagemax: {$pagemax}, nbusers: {$usernb}");
        } else {
            $this->logger->debug("UserSearcherRepository.getUsers: keyword: {$keyword}, in: {$in[0]}, page: {$page}, size: {$pagesize},pagemax: {$pagemax}, nbusers: {$usernb}");
        }

        $statement = $this->connection->prepare($sql);

        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";

        $statement->bindParam(':keyword', $keyword);
        $statement->bindParam(':pagestart', $pagestart, PDO::PARAM_INT);
        $statement->bindParam(':pagesize', $pagesize, PDO::PARAM_INT);

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
            if (empty($in)) {
                $msg = sprintf('No user with keyword [%s] in any field page %d / %d!', str_replace('%', '', $keyword), $page + 1, $pagemax);
            } else {
                $msg = sprintf('No user with keyword [%s] in field [%s] page %d / %d!', str_replace('%', '', $keyword), $in[0], $page + 1, $pagemax);
            }
            $this->logger->info("UserSearcherRepository.getUsers: {$msg}");

            throw new DomainException($msg);
        }

        return $users;
    }

    /**
     * Count number of users.
     *
     * @return int nb of users
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
