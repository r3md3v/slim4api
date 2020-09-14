<?php

namespace App\Action\Auth;

namespace App\Domain\Login\Service;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class LoginManager
{
    /**
     * @var LoginRepository
     */
    private $repository;

    /**
     * @var $tokenLifetime
     */
    private $tokenLifetime;

    /**
     * @var mixed
     */
    private $defaultPage;
    /**
     * @var mixed
     */
    private $defaultPageSize;
    /**
     * @var Tools
     */
    private $ipsSvc;
    /**
     * @var mixed|Tools
     */
    private $tools;

    /**
     * The constructor.
     *
     * @param LoginRepository $repository The repository
     * @param ContainerInterface $ci The container interface
     * @param LoggerFactory $lf The logger Factory
     * @param Tools $ipsSvc
     */
    public function __construct(LoginRepository $repository, ContainerInterface $ci, LoggerFactory $lf, Tools $tools)
    {
        $this->repository = $repository;
        $this->tokenLifetime = $ci->get('settings')['jwt']['lifetime'];
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
        $this->tools = $tools;
    }

    /**
     * Read a login according to username or email, and password.
     *
     * @param string $username The login username or email
     * @param string $password The login password
     * @param string $loglogin The loglogin flag
     *
     * @throws ValidationException
     *
     * @return LoginData The login data
     */
    public function getLoginDetails(string $username, string $password, bool $logLogin): LoginData
    {
        // Get the IP address
        $sourceIp=$this->tools->getUserIpAddr();

        // Feed the logger
        $this->logger->debug("Login.getLoginDetails: input: username: {$username}, sourceip: {$sourceIp}");

        // Validation
        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Get login details with status contening error or 0/1 if user disabled/enabled
        $login = $this->repository->getLoginByUMP($username, $password);
        if ('0' != $login->status && '1' != $login->status) {
            if ($logLogin) {
                $this->repository->logLogin($username, $sourceIp, $login);
            }

            throw new ValidationException($login->status);
        }

        $login->status = 'ok';
        if ($logLogin) {
            $this->repository->logLogin($username, $sourceIp, $login);
        }

        return $login;
    }

    /**
     * Read login list.
     *
     * @param mixed page Page number
     * @param mixed pagesize Nb of lines
     * @param mixed $page
     * @param mixed $pagesize
     *
     * @throws ValidationException
     *
     * @return array LoginList
     */
    public function getLoginList($page, $pagesize): array
    {
        // Feed the logger
        $this->logger->debug("LoginManager.getLoginList: input: page: {$page}, size: {$pagesize}");

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

    /**
     * Cleanup logins.
     *
     * @param mixed $retention
     *
     * @return int nb of rows deleted
     */
    public function cleanupLogins($retention): int
    {
        return $this->repository->cleanupLogin($retention);
    }
}
