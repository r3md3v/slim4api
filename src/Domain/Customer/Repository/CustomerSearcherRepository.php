<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Factory\LoggerFactory;
use DomainException;
use PDO;

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
        $customernb = $this->countCustomers($keyword, $in);

        if (0 == $customernb) {
            $this->logger->info('CustomerSearcherRepository.getCustomers: no customers with keyword ['.$keyword.']'.(!empty($in) ? ' in '.$in[0] : ''));

            throw new DomainException(sprintf('No customer with keyword ['.$keyword.']'.(!empty($in) ? ' in '.$in[0] : '')));
        }
        $pagemax = ceil($customernb / $pagesize);
        $pagestart = (--$page) * $pagesize;

        // Feed the logger & build query
        if (empty($in)) {
            $this->logger->debug("CustomerSearcherRepository.getCustomers: keyword: {$keyword}, in: any, page: {$page}, size: {$pagesize},pagemax: {$pagemax}, nbusers: {$customernb}");
            $sql = 'SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE CUSNAME LIKE :keyword OR CUSADDRESS LIKE :keyword OR CUSCITY LIKE :keyword OR CUSPHONE LIKE :keyword OR CUSEMAIL LIKE :keyword LIMIT :pagestart, :pagesize ;';
        } else {
            $this->logger->debug("CustomerSearcherRepository.getCustomers: keyword: {$keyword}, in: {$in[0]}, page: {$page}, size: {$pagesize},pagemax: {$pagemax}, nbusers: {$customernb}");
            $sql = "SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE {$in[1]} LIKE :keyword LIMIT :pagestart, :pagesize ;";
        }

        $statement = $this->connection->prepare($sql);

        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";

        $statement->bindParam(':keyword', $keyword);
        $statement->bindParam(':pagestart', $pagestart, PDO::PARAM_INT);
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
     * Count number of customers in search.
     *
     * @param string $keyword Word to search
     * @param array  $in      Field exact name/human name
     *
     * @return int nb of customers
     */
    public function countCustomers(string $keyword, array $in): int
    {
        if (empty($in)) {
            $sql = 'SELECT count(*) AS nb FROM customers WHERE CUSNAME LIKE :keyword OR CUSADDRESS LIKE :keyword OR CUSCITY LIKE :keyword OR CUSPHONE LIKE :keyword OR CUSEMAIL LIKE :keyword;';
        } else {
            $sql = "SELECT count(*) AS nb FROM customers WHERE {$in[1]} LIKE :keyword;";
        }
        $statement = $this->connection->prepare($sql);

        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";

        $statement->bindParam(':keyword', $keyword);

        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row['nb'];
    }
}
