<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class CustomerListerRepository
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
     * Get customer list
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @return customers List of Customers
     * @throws DomainException
     *
     */
    public function getCustomers($page = 1, $pagesize = 50): array
    {
        $customernb = $this->countCustomers();

        if ($customernb == 0)
            throw new DomainException(sprintf('No customer!'));

        $pagemax = ceil($customernb / $pagesize);
        $limit = (--$page) * $pagesize;

        $sql = "SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL  FROM customers LIMIT ?, ?;";
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $customers = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $customer = new CustomerData();
            $customer->id = (int)$row['CUSID'];
            $customer->name = (string)$row['CUSNAME'];
            $customer->address = (string)$row['CUSADDRESS'];
            $customer->city = (string)$row['CUSCITY'];
            $customer->phone = (string)$row['CUSPHONE'];
            $customer->email = (string)$row['CUSEMAIL'];
            array_push($customers, $customer);
        }

        if (count($customers) == 0) {
            throw new DomainException(sprintf('No customer page #%d!', ($page + 1)));
        }

        return $customers;
    }

    public function countCustomers(): int
    {
        $sql = "SELECT COUNT(*) AS nb FROM customers;";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['nb'];
    }
}
