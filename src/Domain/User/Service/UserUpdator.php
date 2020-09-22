<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserUpdatorRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class UserUpdator
{
    /**
     * @var UserUpdatorRepository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserUpdatorRepository $repository The repository
     * @param LoggerFactory         $lf         The logger Factory
     */
    public function __construct(UserUpdatorRepository $repository, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Update a user.
     *
     * @param int   $customerId The customer id
     * @param array $data       The form data
     *
     * @throws ValidationException
     *
     * @return int The updated user ID
     */
    public function updateUser(int $userId, array $data): int
    {
        // Input validation
        $this->validateUser($userId, $data);

        // Update user
        if (false == $this->repository->updatetUser($userId, $data)) {
            throw new ValidationException('User '.$userId.' not found and cannot be updated.');
        }

        // Feed the logger
        $this->logger->debug(sprintf('UserUpdator.updateUser: %s updated for id: %s', $data['username'], $userId));
        $this->logger->info(sprintf('UserUpdator.updateUser: updated successfully: %s', $userId));

        return $userId;
    }

    /**
     * Input validation.
     *
     * @param int   $userid The user id
     * @param array $data   The form data
     *
     * @throws ValidationException
     */
    private function validateUser(int $userId, array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library
        if (!isset($data['username']) || !isset($data['password']) || !isset($data['first_name']) || !isset($data['last_name']) || !isset($data['email']) || !isset($data['profile'])) {
            $errors['mandatory'] = 'All fields are not defined';
        } elseif (empty($data['username']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['profile'])) {
            $errors['mandatory'] = 'Input [User Name] [Password] [Firstname] [Lastname] [Email] [Profile] required';
        }

        if (isset($data['email'])) {
            if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email address';
            }
        } else {
            $data['email'] = '';
        }

        if (!empty($errors)) {
            $this->logger->debug(sprintf(
                'updateUser: errors not null: %i,error: %s',
                sizeof($errors),
                (isset($errors['mandatory']) ? $errors['mandatory'].' ' : '').(isset($errors['email']) ? $errors['email'] : '')
            ));

            // Feed the logger
            $this->logger->debug(sprintf(
                'UserUpdator:updateUser: missing params .un:%s, pwd: %s, fn: %s, ln: %s, eml: %s, prof: %s',
                isset($data['username']) ? $data['username'] : '',
                isset($data['password']) ? '[Hidden]' : '',
                isset($data['first_name']) ? $data['first_name'] : '',
                isset($data['last_name']) ? $data['last_name'] : '',
                isset($data['email']) ? $data['email'] : '',
                isset($data['profile']) ? $data['profile'] : ''
            ));

            throw new ValidationException('Please check your input.', $errors);
        }

        if (true == $this->repository->userExists($userId, $data['username'], $data['email'])) {
            throw new ValidationException('User already exists with name ['.$data['username'].'] or email ['.$data['email'].']', $errors);
        }
    }
}
