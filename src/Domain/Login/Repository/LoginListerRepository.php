<?php

namespace App\Domain\Login\Repository;

use App\Domain\Login\Data\LoginData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class LoginListerRepository
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
    public function getLogins($page = 1, $pagesize = 50): array
    {
        $loginnb = $this->countLogins();

        if (0 == $loginnb) {
            throw new DomainException(sprintf('No login!'));
        }
        $pagemax = ceil($loginnb / $pagesize);
        $limit = (--$page) * $pagesize;

        $sql = 'SELECT JWUID, JWUUSERNAME, JWUEMAIL, JWUDESCRIPTION, JWULASTTOKEN, JWUSTATUS FROM usersjwt LIMIT ?, ?;';
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $logins = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $login = new LoginData();
            $login->id = (int) $row['JWUID'];
            $login->username = (string) $row['JWUUSERNAME'];
            $login->email = (string) $row['JWUEMAIL'];
            $login->description = (string) $row['JWUDESCRIPTION'];
            $login->lasttoken = (string) $row['JWULASTTOKEN'];
            $login->status = (string) $row['JWUSTATUS'];
            array_push($logins, $login);
        }

        if (0 == count($logins)) {
            throw new DomainException(sprintf('No item page %d / %d!', $page + 1, $pagemax));
        }

        return $logins;
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

        return $row['nb'];
    }
}
