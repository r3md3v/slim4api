<?php

namespace Tests\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerDeletorRepository;
use App\Domain\Customer\Service\CustomerDeletor;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class CustomerDeletorTest extends TestCase
{
    use AppTestTrait;

    public function testGetCustomerDetailsKoEmptyParam()
    {
        // Mock the required repository method
        $this->mock(CustomerDeletorRepository::class)->method('deleteCustomerById');

        $service = $this->container->get(CustomerDeletor::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Customer ID required");
        $actual = $service->deleteCustomer(null);

    }

    public function testGetCustomerDetailsKoNoUserFound()
    {
        $msgExpected= sprintf('Customer not found: %s', "1");

        // Mock the required repository method
        $this->mock(CustomerDeletorRepository::class)->method('deleteCustomerById')
            ->with(1)
            ->willThrowException(new DomainException($msgExpected));

        $service = $this->container->get(CustomerDeletor::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($msgExpected);
        $actual = $service->deleteCustomer(1);

    }
}
