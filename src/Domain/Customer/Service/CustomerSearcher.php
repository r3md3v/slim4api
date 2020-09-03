<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerSearcherRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
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
     * @param ContainerInterface         $ci         The container interface
     * @param LoggerFactory              $lf         The logger Factory
     */
    public function __construct(CustomerSearcherRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->defaultSearchField = $ci->get('settings')['db']['defaultSearchFieldCustomer'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Search customer list.
     *
     * @param string $keyword  Word to search
     * @param mixed  $in       Field number
     * @param mixed  $page     Page number
     * @param mixed  $pagesize Nb of lines
     *
     * @throws ValidationException
     *
     * @return CustomerSearch
     */
    public function getCustomerSearch(string $keyword, $in, $page, $pagesize): array
    {
        // Feed the logger
        $this->logger->debug("CustomerSearcher.getCustomerSearch: input: page: {$page}, size: {$pagesize}");

        // Validation

        if (!is_numeric($page) || $page < $this->defaultPage) {
            $page = $this->defaultPage;
        }

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize) {
            $pagesize = $this->defaultPageSize;
        }

        if (!is_numeric($in) || $in < 1 || $in > count($this->defaultSearchField)) {
            $in = -1;
        } else {
            $in = $this->defaultSearchField[$in - 1];
        }

        if (empty($keyword)) {
            throw new ValidationException('Keyword required');
        }

        return $this->repository->getCustomers($keyword, $in, $page, $pagesize);
    }
}
