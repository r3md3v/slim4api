<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Factory\LoggerFactory;
use DomainException;
use PDO;
use function PHPUnit\Framework\isEmpty;

/**
 * Repository.
 */
class CustomerSearcherRepository
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
     * Get customer search.
     *
     * @param string $keyword  Word to search
     * @param array  $in       Field exact name/human name
     * @param int    $page     page number
     * @param int    $pagesize page size
     *
     * @throws DomainException
     *
     * @return array customers Search of Customers
     */
    public function getCustomers(string $keyword, array $in, int $page, int $pagesize): array
    {
        $customernb = $this->countCustomers();

        if (0 == $customernb) {
            $this->logger->info('CustomerSearcherRepository.getCustomers: no customers in table');

            throw new DomainException(sprintf('No customer!'));
        }
        $pagemax = ceil($customernb / $pagesize);
        $limit = (--$page) * $pagesize;

        if ( isEmpty($in)) {
            $sql = 'SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE CUSNAME LIKE :keyword OR CUSADDRESS LIKE :keyword OR CUSCITY LIKE :keyword OR CUSPHONE LIKE :keyword OR CUSEMAIL LIKE :keyword LIMIT :limit, :pagesize ;';
        } else {
            $sql = "SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE {$in[1]} LIKE :keyword LIMIT :limit, :pagesize ;";
        }

        // Feed the logger
        $this->logger->debug("CustomerSearcherRepository.getCustomers: keyword: {$keyword}, in: {$in[0]}, page: {$page}, size: {$pagesize},pagemax: {$pagemax}, nbusers: {$customernb}");

        $statement = $this->connection->prepare($sql);

        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";

        $statement->bindParam(':keyword', $keyword);
        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':pagesize', $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $customers = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $customer = new CustomerData(
                (int) $row['CUSID'],
                (string) $row['CUSNAME'],
                (string) $row['CUSADDRESS'],
                (string) $row['CUSCITY'],
                (string) $row['CUSPHONE'],
                (string) $row['CUSEMAIL']
            );

            array_push($customers, $customer);
        }

        if (0 == count($customers)) {
            if (empty($in)) {
                $msg = sprintf('No customer with keyword [%s] in any field page %d / %d!', str_replace('%', '', $keyword), $page + 1, $pagemax);
            } else {
                $msg = sprintf('No customer with keyword [%s] in field [%s] page %d / %d!', str_replace('%', '', $keyword), $in[0], $page + 1, $pagemax);
            }
            $this->logger->info("CustomerSearcherRepository.getCustomers: {$msg}");

            throw new DomainException($msg);
        }

        return $customers;
    }

    /**
     * Count number of customers.
     *
     * @return int nb of users
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
