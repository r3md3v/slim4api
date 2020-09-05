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
     * @param ContainerInterface $ci The container interface
     * @param LoggerFactory $lf The logger Factory
     */
    public function __construct(CustomerListerRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read Customer list.
     *
     * @param mixed $page Page number
     * @param mixed $pagesize Nb of lines
     *
     * @return CustomerList
     * @throws ValidationException
     *
     */
    public function getCustomerList(int $page = 1, int $pagesize = 50): array
    {
        // Feed the logger
        $this->logger->debug("CustomerLister.getCustomerList: input: page: {$page}, size: {$pagesize}");

        // Validation

        if (!is_numeric($page) || $page < $this->defaultPage) {
            $page = $this->defaultPage;
        }

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize) {
            $pagesize = $this->defaultPageSize;
        }

        $this->logger->debug("CustomerList.getCustomerList: page: $page, size: $pagesize");
        return $this->repository->getCustomers($page, $pagesize);

    }

    /**
     * Count customers.
     *
     * @return nb
     */
    public
    function getCustomerCount(): int
    {
        return $this->repository->countCustomers();
    }
}
