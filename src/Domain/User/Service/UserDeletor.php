<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserDeletorRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class UserDeletor
{
    /**
     * @var UserDeletorRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param UserDeletorRepository $repository The repository
     */
    public function __construct(UserDeletorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Delete a user by the given user id.
     *
     * @param int $userId The user id
     *
     * @throws ValidationException
     *
     * @return id or error message
     */
    public function deleteUser(?int $userId)
    {
        // Validation
        if (empty($userId)) {
            throw new ValidationException('User ID required');
        }

        return $this->repository->deleteUserById($userId);
    }
}
