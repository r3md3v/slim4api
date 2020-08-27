<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerCreatorRepository;
use App\Exception\ValidationException;
use Slim\Logger;

/**
 * Service.
 */
final class CustomerCreator
{
    /**
     * @var CustomerCreatorRepository
     */
    private $repository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerCreatorRepository $repository The repository
     * @param Logger $logger
     */
    public function __construct(CustomerCreatorRepository $repository, Logger $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Create a new customer.
     *
     * @param array $data The form data
     *
     * @return int The new customer ID
     */
    public function createCustomer(array $data): int
    {
        // Input validation
        $this->validateNewCustomer($data);
        //$this->logger->debug(sprintf("createCustomer: %s",var_dump($data)));

        // Insert customer
        $customerId = $this->repository->insertCustomer($data);

        // Logging here: Customer created successfully
        $this->logger->debug(sprintf("customer %s created with id: %s", $data['cusname'], $customerId));
        $this->logger->info(sprintf('Customer created successfully: %s', $customerId));

        return $customerId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @return void
     * @throws ValidationException
     *
     */
    private function validateNewCustomer(array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library

        if (empty($data['cusname']) || empty($data['address']) || empty($data['city']) || empty($data['email'])) {
            $errors['mandatory'] = 'Input [Customer Name] [Address] [City] [Email] required';
        }

        if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address for customer';
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException('Please check your input.', $errors);
        }

        if (true == $this->repository->customerExists($data['email'])) {
            throw new ValidationException('Customer already exists with email ' . $data['email'] . '.', $errors);
        }
    }
}