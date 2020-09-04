<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use DomainException;
use PDO;

/**
 * Repository.
 */
class CustomerReaderRepository
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
     * Get Customer by the given Customer id.
     *
     * @param int $CustomerId The Customer id
     *
     * @return CustomerData The Customer data
     * @throws DomainException
     *
     */
    public function getCustomerById(int $CustomerId): CustomerData
    {
        $sql = "SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL  FROM customers WHERE CUSID = :id;";
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $CustomerId]);

        $row = $statement->fetch();

        if (!$row) {
            throw new DomainException(sprintf('Customer not found: %s', $CustomerId));
        }

        // Map array to data object
        $customer = new CustomerData();
        $customer->id = (int)$row['CUSID'];
        $customer->name = (string)$row['CUSNAME'];
        $customer->address = (string)$row['CUSADDRESS'];
        $customer->city = (string)$row['CUSCITY'];
        $customer->phone = (string)$row['CUSPHONE'];
        $customer->email = (string)$row['CUSEMAIL'];

        return $customer;
    }
}
