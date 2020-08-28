<?php

namespace App\Domain\Login\Service;

use App\Domain\Login\Repository\LoginListerRepository;
use App\Exception\ValidationException;
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
    private $defaultPage;
    private $defaultPageSize;

    /**
     * The constructor.
     *
     * @param LoginListerRepository $repository The repository
     */
    public function __construct(LoginListerRepository $repository, ContainerInterface $ci)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
    }

    /**
     * Read login list.
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @throws ValidationException
     *
     * @return LoginList
     */
    public function getLoginList(int $page, int $pagesize): array
    {
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
     * @return nb
     */
    public function getLoginCount(): int
    {
        return $this->repository->countLogins();
    }
}
