<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerSearcherRepository;
use App\Exception\ValidationException;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class CustomerSearcher
{
    /**
     * @var CustomerSearcherRepository
     */
    private $repository;
    private $defaultPage;
    private $defaultPageSize;
    private $defaultSearchField;

    /**
     * The constructor.
     *
     * @param CustomerSearcherRepository $repository The repository
     */
    public function __construct(CustomerSearcherRepository $repository, ContainerInterface $ci)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->defaultSearchField = $ci->get('settings')['db']['defaultSearchFieldCustomer'];
    }

    /**
     * Search customer list
     *
     * @param string keyword Word to search
 	 * @param int in Field number
 	 * @param int page Page number
	 * @param int pagesize Nb of lines
     *
     * @throws ValidationException
     *
     * @return CustomerSearch
     */
    public function getCustomerSearch(string $keyword, int $in, int $page, int $pagesize): array
    {
		// Validation
 
        if (!is_numeric($page) || $page < $this->defaultPage)
            $page = $this->defaultPage;

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize)
            $pagesize = $this->defaultPageSize;

        if (!is_numeric($in) || $in < 1 || $in > count($this->defaultSearchField))
            $in = -1;
            else $in = $this->defaultSearchField[$in-1];

        if (empty($keyword)) {
            throw new ValidationException('Keyword required');
        }

        $customers = $this->repository->getCustomers($keyword, $in, $page, $pagesize);

        return $customers;
    }
}