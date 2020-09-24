<?php

namespace App\Domain\Customer\Repository;

use PDO;

/**
 * Repository.
 */
class CustomerUpdatorRepository
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
     * Update customer row.
     *
     * @param int   $customerId The customer id
     * @param array $customer   The customer data
     *
     * @return bool row updated
     */
    public function updateCustomer(int $customerId, array $customer): bool
    {
        $paramSql = [
            'cusname' => $customer['cusname'],
            'address' => $customer['address'],
            'city' => $customer['city'],
            'phone' => $customer['phone'],
            'email' => $customer['email'],
            'id' => $customerId,
        ];

        $sql = "UPDATE customers SET 
                CUSNAME=:cusname, 
                CUSADDRESS=:address, 
                CUSCITY=:city, 
				CUSPHONE=:phone, 
                CUSEMAIL=:email, 
                CUSUPDATEDBY='slim4api', 
                CUSUPDATEDAT=now()
				WHERE CUSID=:id;";

        $statement = $this->connection->prepare($sql);
        $statement->execute($paramSql);

        return $statement->rowCount() > 0;
    }

    /**
     * check if email is already in database for other id.
     *
     * @param customerId
     * @param email
     *
     * @return bool rowCount exists
     */
    public function customerExists(int $customerId, string $email): bool
    {
        $params = [];
        $params['email'] = $email;
        $params['id'] = $customerId;

        $sql = 'SELECT * FROM customers AS u 
                WHERE u.CUSEMAIL =:email AND u.CUSID <>:id;';

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount() > 0;
    }
}
