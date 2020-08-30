<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserListerRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class UserLister
{
    /**
     * @var UserListerRepository
     */
    private $repository;
    private $defaultPage;
    private $defaultPageSize;

    /**
     * The constructor.
     *
     * @param UserListerRepository $repository The repository
     * @param ContainerInterface $ci
     * @param LoggerFactory $lf
     */
    public function __construct(UserListerRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read user list.
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @return UserList
     * @throws ValidationException
     *
     */
    public function getUserList(int $page, int $pagesize): array
    {
        // Validation

        if (!is_numeric($page) || $page < $this->defaultPage) {
            $page = $this->defaultPage;
        }

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize) {
            $pagesize = $this->defaultPageSize;
        }

        $this->logger->debug("UserLister.getUserList: page: $page, size: $pagesize");
        $users = $this->repository->getUsers($page, $pagesize);
    }

    /**
     * Count users.
     *
     * @return nb
     */
    public function getUserCount(): int
    {
        return $this->repository->countUsers();
    }
}
