<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerDeletorRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class CustomerDeletor
{
    /**
     * @var CustomerDeletorRepository
     */
    private $repository;

    /**
     * The constructor.
     *
     * @param CustomerDeletorRepository $repository The repository
     */
    public function __construct(CustomerDeletorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Delete a customer by the given customer id.
     *
     * @param int $customerId The customer id
     *
     * @throws ValidationException
     *
     * @return id or error message
     */
    public function deleteCustomer(?int $customerId)
    {
        // Validation
        if (empty($customerId)) {
            throw new ValidationException('Customer ID required');
        }

        return $this->repository->deleteCustomerById($customerId);
    }
}
