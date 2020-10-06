<?php

namespace Tests\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerReaderRepository;
use App\Domain\Customer\Service\CustomerReader;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class CustomerReaderTest extends TestCase
{
    use AppTestTrait;

    public function testGetCustomerDetailsOK()
    {
        $cd = new CustomerData(1, "name", "address", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(CustomerReaderRepository::class)->method('getCustomerById')->with('1')->willReturn($cd);

        $service = $this->container->get(CustomerReader::class);

        $actual = $service->getCustomerDetails(1);

        static::assertSame($cd, $actual);
    }

    public function testGetCustomerDetailsKoEmptyParam()
    {
        // Mock the required repository method
        $this->mock(CustomerReaderRepository::class)->method('getCustomerById');

        $service = $this->container->get(CustomerReader::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Customer ID required");
        $actual = $service->getCustomerDetails(null);

    }

    public function testGetCustomerDetailsKoNoUserFound()
    {
        $msgExpected= sprintf('Customer not found: %s', "1");

        // Mock the required repository method
        $this->mock(CustomerReaderRepository::class)->method('getCustomerById')
            ->with(1)
            ->willThrowException(new DomainException($msgExpected));

        $service = $this->container->get(CustomerReader::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($msgExpected);
        $actual = $service->getCustomerDetails(1);

    }
}
