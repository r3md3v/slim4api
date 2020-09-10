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
     * @var tokenlifetime
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
     * The constructor.
     *
     * @param LoginRepository    $repository The repository
     * @param ContainerInterface $ci         The container interface
     * @param LoggerFactory      $lf         The logger Factory
     */
    public function __construct(LoginRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->tokenLifetime = $ci->get('settings')['jwt']['lifetime'];
        $this->defaultPage = $ci->get('settings')['db']['defaultPage'];
        $this->defaultPageSize = $ci->get('settings')['db']['defaultPageSize'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read a login according to username or email, and password.
     *
     * @param string $username The login username or email
     * @param string $password The login password
     * @param string $sourceip The source IP
     * @param string $loglogin The loglogin flag
     *
     * @throws ValidationException
     *
     * @return LoginData The login data
     */
    public function getLoginDetails(string $username, string $password, string $sourceip, int $loglogin): LoginData
    {
        // Feed the logger
        $this->logger->debug("Login.getLoginDetails: input: username: {$username}, sourceip: {$sourceip}");

        // Validation
        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Get login details with status contening error or 0/1 if user disabled/enabled
        $login = $this->repository->getLoginByUMP($username, $password);
        if ('0' != $login->status && '1' != $login->status) {
            if ($loglogin) {
                $this->repository->logLogin($username, $sourceip, $login);
            }

            throw new ValidationException($login->status);
        }

        $login->status = 'ok';
        if ($loglogin) {
            $this->repository->logLogin($username, $sourceip, $login);
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
     * Retrieves IP address from request header.
     *
     * @return ipAddress The Source IP
     */
    public function getUserIpAddr()
    {
        $ipAddress = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->isValidIpAddress($_SERVER['HTTP_CLIENT_IP'])) {
            // check for shared ISP IP
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check for IPs passing through proxy servers
            // check if multiple IP addresses are set and take the first one
            $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipAddressList as $ip) {
                if ($this->isValidIpAddress($ip)) {
                    $ipAddress = $ip;

                    break;
                }
            }
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->isValidIpAddress($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->isValidIpAddress($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->isValidIpAddress($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED']) && $this->isValidIpAddress($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR']) && $this->isValidIpAddress($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        return $ipAddress;
    }

    /**
     * Is IP address is both a valid, and does not fall within a private network range.
     *
     * @param string $ip
     *
     * @return bool
     */
    public function isValidIpAddress($ip)
    {
        if (false === filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        return true;
    }

    /**
     * Return source IP of client.
     *
     * @return ip The Source IP
     */
    public function getUserIpAddrLight()
    {
        // $_SERVER['REMOTE_ADDR'] might not returns the correct IP address of the user in case of Proxy.

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Cleanup logins.
     *
     * @param mixed $retention
     *
     * @return nb nb of rows deleted
     */
    public function cleanupLogins($retention): int
    {
        return $this->repository->cleanupLogin($retention);
    }
}
