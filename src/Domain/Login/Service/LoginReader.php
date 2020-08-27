<?php

namespace App\Action\Auth;

//namespace App\Domain\Login\Service;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginReaderRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class LoginReader
{
    /**
     * @var LoginReaderRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param LoginReaderRepository $repository The repository
     */
    public function __construct(LoginReaderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Read a login according to username+password.
     *
     * @param string $username The login username
     * @param string $password The login password
     *
     * @throws ValidationException
     *
     * @return LoginData The login data
     */
    public function getLoginDetails(string $username, string $password): LoginData
    {
        // Validation
        if (empty($username) || empty($password)) {
            throw new ValidationException('Username and password required');
        }

        return $this->repository->getLoginByUP($username, $password);
    }
}
