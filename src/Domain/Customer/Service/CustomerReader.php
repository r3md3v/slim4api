<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerReaderRepository;
use App\Exception\ValidationException;

/**
 * Service.
 */
final class CustomerReader
{
    /**
     * @var CustomerReaderRepository
     */
    private $repository;

    /**
     * The constructor.
     * @codeCoverageIgnore
     *
     * @param CustomerReaderRepository $repository The repository
     */
    public function __construct(CustomerReaderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Read a Customer by the given Customer id.
     *
     * @param int $CustomerId The Customer id
     *
     * @return CustomerData The Customer data
     * @throws ValidationException
     *
     */
    public function getCustomerDetails($CustomerId): CustomerData
    {
        // Validation
        if (empty($CustomerId)) {
            throw new ValidationException('Customer ID required');
        }

        $Customer = $this->repository->getCustomerById($CustomerId);

        return $Customer;
    }
}