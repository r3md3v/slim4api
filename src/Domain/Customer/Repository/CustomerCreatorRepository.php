<?php

namespace App\Domain\Customer\Repository;

use PDO;

/**
 * Repository.
 */
class CustomerCreatorRepository
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
     * Insert customer row.
     *
     * @param array $customer The customer
     *
     * @return int The new ID
     */
    public function insertCustomer(array $customer): int
    {
        $paramSql = [
            'cusname' => $customer['cusname'],
            'address' => $customer['address'],
            'city' => $customer['city'],
            'phone' => $customer['phone'],
            'email' => $customer['email'],
        ];

        $sql = "INSERT INTO customers SET 
                CUSNAME=:cusname, 
                CUSADDRESS=:address, 
                CUSCITY=:city, 
				CUSPHONE=:phone, 
                CUSEMAIL=:email, 
                CUSUPDATEDBY='slim4api', 
                CUSUPDATEDAT=now();";

        $this->connection->prepare($sql)->execute($paramSql);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * check if email is already in database.
     *
     * @param email
     *
     * @return bool rowCount
     */
    public function customerExists(string $email): bool
    {
        $params = [];
        $params['email'] = $email;

        $sql = 'SELECT * FROM customers AS u 
                WHERE u.CUSEMAIL =:email;';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }
}
