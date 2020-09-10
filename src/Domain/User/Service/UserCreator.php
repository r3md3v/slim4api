<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserCreatorRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class UserCreator
{
    /**
     * @var UserCreatorRepository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserCreatorRepository $repository The repository
     */
    public function __construct(UserCreatorRepository $repository, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Create a new user.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     *
     * @return int The new user ID
     */
    public function createUser(array $data): int
    {
        // Input validation
        $this->validateNewUser($data);
        //$this->logger->debug(sprintf("createUser: %s",var_dump($data)));

        // Insert user
        $userId = $this->repository->insertUser($data);

        // Feed the logger
        $this->logger->debug(sprintf('UserCreator.createUser: %s created with id: %s', $data['username'], $userId));
        $this->logger->info(sprintf('UserCreator.createUser: created successfully: %s', $userId));

        return $userId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     */
    private function validateNewUser(array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library

        if (empty($data['username']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['profile'])) {
            $errors['mandatory'] = 'Input [User  Name] [Password] [Firstname] [Lastname] [Email] [Profile] required';
            $this->logger->debug(sprintf(
                'UserCreator:createUSer: missing params .names:%s, pwd: %s, fn: %s, ln: %s, eml: %s, prof: %s',
                $data['username'],
                $data['password'],
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['profile']
            ));
        }

        if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }

        if (!empty($errors)) {
            throw new ValidationException('Please check your input.', $errors);
        }

        if ($this->repository->userExists($data['username'], $data['email'])) {
            throw new ValidationException('User name already exists with name '.$data['username'].' or email '.$data['email'].'.', $errors);
        }
    }
}
