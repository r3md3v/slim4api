<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerUpdatorRepository;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Service.
 */
final class CustomerUpdator
{
    /**
     * @var CustomerUpdatorRepository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param CustomerUpdatorRepository $repository The repository
     * @param LoggerFactory             $lf         The logger Factory
     */
    public function __construct(CustomerUpdatorRepository $repository, LoggerFactory $lf)
    {
        $this->repository = $repository;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Update a customer.
     *
     * @param int   $customerId The customer id
     * @param array $data       The form data
     *
     * @throws ValidationException
     *
     * @return int The updated customer ID
     */
    public function updateCustomer(int $customerId, array $data): int
    {
        // Input validation
        $this->validateCustomer($customerId, $data);

        // Update customer
        if (false == $this->repository->updateCustomer($customerId, $data)) {
            throw new ValidationException('Customer '.$customerId.' not found and cannot be updated.');
        }

        // Feed the logger
        $this->logger->debug(sprintf('CustomerUpdator.updateCustomer: %s updated for id: %s', $data['cusname'], $customerId));
        $this->logger->info(sprintf('CustomerUpdator.updateCustomer: updated successfully %s', $customerId));

        return $customerId;
    }

    /**
     * Input validation.
     *
     * @param int   $customerid The customer id
     * @param array $data       The form data
     *
     * @throws ValidationException
     */
    private function validateCustomer(int $customerId, array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library

        if (!isset($data['cusname']) || !isset($data['address']) || !isset($data['city']) || !isset($data['email'])) {
            $errors['mandatory'] = 'All fields are not defined';
        } elseif (empty($data['cusname']) || empty($data['address']) || empty($data['city']) || empty($data['email'])) {
            $errors['mandatory'] = 'Input [Customer Name] [Address] [City] [Email] required';
        }

        if (isset($data['email'])) {
            if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email address for customer';
            }
        } else {
            $data['email'] = '';
        }

        if (!empty($errors)) {
            $this->logger->debug(sprintf(
                'updateCustomer: errors not null: %i,error: %s',
                sizeof($errors),
                (isset($errors['mandatory']) ? $errors['mandatory'].' ' : '').(isset($errors['email']) ? $errors['email'] : '')
            ));

            // Feed the logger
            $this->logger->debug(sprintf(
                'CustomerUpdator:updateCustomer: missing params .name:%s, addr: %s, city: %s, phone: %s, eml: %s',
                isset($data['cusname']) ? $data['cusname'] : '',
                isset($data['address']) ? $data['address'] : '',
                isset($data['city']) ? $data['city'] : '',
                isset($data['phone']) ? $data['phone'] : '',
                isset($data['email']) ? $data['email'] : ''
            ));

            throw new ValidationException('Please check your input.', $errors);
        }

        if (true == $this->repository->customerExists($customerId, $data['email'])) {
            throw new ValidationException('Customer already exists with email ['.$data['email'].']', $errors);
        }
    }
}
