<?php

namespace App\Action\Auth;

namespace App\Domain\Login\Service;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginReaderRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * Service.
 */
final class LoginReader
{
    /**
     * @var LoginReaderRepository
     */
    private $repository;
    private $tokenlifetime;

    /**
     * The constructor.
     *
     * @param LoginReaderRepository $repository The repository
     * @param ContainerInterface $ci The container interface
     * @param LoggerFactory $lf The logger Factory
     */
    public function __construct(LoginReaderRepository $repository, ContainerInterface $ci, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->tokenlifetime = $ci->get('settings')['jwt']['lifetime'];
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Read a login according to username or email, and password.
     *
     * @param string $username The login username or email
     * @param string $password The login password
     * @param string $sourceip The source IP
     *
     * @return LoginData The login data
     * @throws ValidationException
     *
     */
    public function getLoginDetails(string $username, string $password, string $sourceip): LoginData
    {
        // Feed the logger
        $this->logger->debug("LoginReader.getLoginDetails: input: username: {$username}, sourceip: {$sourceip}");

        // Validation
        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        // Get login details with status contening error or 0/1 if user diabled/enabled
        $login = $this->repository->getLoginByUMP($username, $password);
        if ('0' != $login->status && '1' != $login->status) {
            $this->repository->logLogin($username, $sourceip, $login);

            throw new ValidationException($login->status);
        }

        $login->status = 'ok';
        $this->repository->logLogin($username, $sourceip, $login);

        return $login;
    }

    /**
     * Return source IP of client.
     *
     * @return sourceip The Srouce IP
     */
    public function getUserIpAddr()
    {
        // $_SERVER['REMOTE_ADDR'] might not returns the correct IP address of the user in case of Proxy.
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
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
