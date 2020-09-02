<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Factory\LoggerFactory;
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
     * @param PDO           $connection The database connection
     * @param LoggerFactory $lf         The logger Factory
     */
    public function __construct(PDO $connection, LoggerFactory $lf)
    {
        $this->connection = $connection;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Get customer list.
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     * @param mixed $page
     * @param mixed $pagesize
     *
     * @return customers List of Customers
     * @throws DomainException
     *
     */
    public function getCustomers(int $page = 1, int $pagesize = 50): array
    {
        // Feed the logger
        $this->logger->debug("CustomerListerRepository.getCustomers: page: {$page}, size: {$pagesize}");

        $customernb = $this->countCustomers();

        if (0 == $customernb) {
            throw new DomainException(sprintf('No customer!'));
        }
        $pagemax = ceil($customernb / $pagesize);
        $limit = (--$page) * $pagesize;

        $sql = 'SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL  FROM customers LIMIT ?, ?;';
        $statement = $this->connection->prepare($sql);

        $statement->bindParam(1, $limit, PDO::PARAM_INT);
        $statement->bindParam(2, $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $customers = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $customer = new CustomerData();
            $customer->id = (int) $row['CUSID'];
            $customer->name = (string) $row['CUSNAME'];
            $customer->address = (string) $row['CUSADDRESS'];
            $customer->city = (string) $row['CUSCITY'];
            $customer->phone = (string) $row['CUSPHONE'];
            $customer->email = (string) $row['CUSEMAIL'];
            array_push($customers, $customer);
        }

        if (0 == count($customers)) {
            throw new DomainException(sprintf('No item page %d / %d', $page + 1, $pagemax));
        }

        return $customers;
    }

    /**
     * Get customer count.
     *
     * @return nb Nb of Customers
     */
    public function countCustomers(): int
    {
        $sql = 'SELECT COUNT(*) AS nb FROM customers;';
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row['nb'];
    }
}
