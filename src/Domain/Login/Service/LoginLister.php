<?php

namespace App\Domain\Login\Service;

use App\Domain\Login\Repository\LoginListerRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class LoginLister
{
    /**
     * @var LoginListerRepository
     */
    private $repository;
    /**
     * @var mixed
     */
    private $defaultPage;
    /**
     * @var mixed
     */
    private $defaultPageSize;

    /**
     * The constructor.
     *
     * @param LoginListerRepository $repository The repository
     * @param ContainerInterface $ci The container interface
     * @param LoggerFactory $lf The logger Factory
     */
    public function __construct(LoginListerRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read login list.
     *
     * @param mixed page Page number
     * @param mixed pagesize Nb of lines
     *
     * @return array LoginList
     * @throws ValidationException
     *
     */
    public function getLoginList($page, $pagesize): array
    {
        // Feed the logger
        $this->logger->debug("LoginLister.getLoginList: input: page: {$page}, size: {$pagesize}");

        // Validation

        if (!is_numeric($page) || $page < $this->defaultPage) {
            $page = $this->defaultPage;
        }

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize) {
            $pagesize = $this->defaultPageSize;
        }

        return $this->repository->getLogins($page, $pagesize);
    }

    /**
     * Count logins.
     *
     * @return int nb
     */
    public function getLoginCount(): int
    {
        return $this->repository->countLogins();
    }
}
