<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserSearcherRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class UserSearcher
{
    /**
     * @var UserSearcherRepository
     */
    private $repository;
    private $defaultPage;
    private $defaultPageSize;
    private $defaultSearchField;

    /**
     * The constructor.
     *
     * @param UserSearcherRepository $repository The repository
     * @param ContainerInterface     $ci         The container interface
     * @param LoggerFactory          $lf         The logger Factory
     */
    public function __construct(UserSearcherRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->defaultSearchField = $ci->get('settings')['db']['defaultSearchFieldUser'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Search user list.
     *
     * @param string $keyword  Word to search
     * @param mixed  $in       Field number
     * @param mixed  $page     Page number
     * @param mixed  $pagesize Nb of lines
     *
     * @return UserSearch
     * @throws ValidationException
     *
     */
    public function getUserSearch(string $keyword, int $in, int $page, int $pagesize): array
    {
        // Validation
        $this->logger->debug("UserSearcher.getUser: input: keyword: $keyword, in: $in, page: $page, size: $pagesize");

        if (!is_numeric($page) || $page < 1 || $page < $this->defaultPage)
        // Feed the logger
        $this->logger->debug("UserSearcher.getUserSearch: input: keyword: {$keyword}, in: {$in}, page: {$page}, size: {$pagesize}");

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
        $this->logger->debug("UserSearcher.getUser: output: keyword: $keyword, in: $in, page: $page, size: $pagesize");
        $users = $this->repository->getUsers($keyword, $in, $page, $pagesize);

        return $users;
    }
}
