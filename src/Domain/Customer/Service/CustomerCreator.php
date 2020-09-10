<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerCreatorRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerCreatorRepository $repository The repository
     * @param LoggerFactory             $lf         The logger Factory
     */
    public function __construct(CustomerCreatorRepository $repository, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Create a new customer.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     *
     * @return int The new customer ID
     */
    public function createCustomer(array $data): int
    {
        // Input validation
        $this->validateNewCustomer($data);

        // Insert customer
        $customerId = $this->repository->insertCustomer($data);

        // Feed the logger
        $this->logger->debug(sprintf('CustomerCreator.createCustomer: %s created with id: %s', $data['cusname'], $customerId));
        $this->logger->info(sprintf('CustomerCreator.createCustomer: created successfully %s', $customerId));

        return $customerId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
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

        if (!empty($errors)) {
            $this->logger->debug(sprintf(
                'createCustomer: errors not null: %i,error: %s',
                sizeof($errors),
                (isset($errors['mandatory']) ? $errors['mandatory'].' ' : '').(isset($errors['email']) ? $errors['email'] : '')
            ));

            // Feed the logger
            $this->logger->debug(sprintf(
                'CustomerCreator:createCustomer: missing params .name:%s, addr: %s, city: %s, ln: %s, eml: %s',
                $data['cusname'],
                $data['address'],
                $data['city'],
                $data['email'],
            ));

            throw new ValidationException('Please check your input.', $errors);
        }

        if (true == $this->repository->customerExists($data['email'])) {
            throw new ValidationException('Customer already exists with email ['.$data['email'].']', $errors);
        }
    }
}
