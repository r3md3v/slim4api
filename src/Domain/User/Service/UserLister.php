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
     */
    public function __construct(UserListerRepository $repository, ContainerInterface $ci)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
    }

    /**
     * Read user list
     *
     * @param int page Page number
     * @param int pagesize Nb of lines
     *
     * @throws ValidationException
     *
     * @return UserList
     */
    public function getUserList(int $page, int $pagesize): array
    {
        // Validation
 
        if (!is_numeric($page) || $page < $this->defaultPage)
           $page = $this->defaultPage;

        if (!is_numeric($pagesize) || $pagesize < 1 || $pagesize > $this->defaultPageSize)
           $pagesize = $this->defaultPageSize;
   
        $users = $this->repository->getUsers($page, $pagesize);

        return $users;
    }
}