<?php

namespace App\Domain\Customer\Repository;

use DomainException;
use PDO;

/**
 * Repository.
 */
class CustomerDeletorRepository
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
     * Delete Customer by the given Customer id.
     *
     * @param int $customerId The Customer id
     *
     * @throws DomainException
     *
     * @return int $customerId
     */
    public function deleteCustomerById(int $customerId)
    {
        $sql = 'DELETE FROM customers WHERE CUSID = :id;';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $customerId]);

        if (0 == $statement->rowCount()) {
            throw new DomainException(sprintf('Customer not found: %s', $customerId));
        }

        return (int) $customerId;
    }
}
