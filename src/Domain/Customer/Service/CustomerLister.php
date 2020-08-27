<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerListerRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class CustomerLister
{
    /**
     * @var CustomerListerRepository
     */
    private $repository;
    /**
     * @var mixed
     */
    private $defaultPageSize;
    /**
     * @var mixed
     */
    private $defaultPage;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerListerRepository $repository The repository
     */
    public function __construct(CustomerListerRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read Customer list
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @return CustomerList
     * @throws ValidationException
     *
     */
    public function getCustomerList(int $page = 1, int $pagesize = 50): array
    {
        if (!is_numeric($page) or ($page < $this->defaultPage)) $page = $this->defaultPage;
        if (!is_numeric($pagesize) or ($pagesize < $this->defaultPageSize)) $pagesize = $this->defaultPageSize;

        $this->logger->debug("getCustomerList: page: $page, pagesize: $pagesize");
        $customers = $this->repository->getCustomers($page, $pagesize);

        // Validation
        if (count($customers) == 0) {
            throw new ValidationException('No customer!');
        }

        return $customers;
    }
}