<?php

namespace Tests\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerReaderRepository;
use App\Domain\Customer\Service\CustomerReader;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class CustomerReaderTest extends TestCase
{
    use AppTestTrait;

    public function testGetCustomerDetailsOK()
    {
        $cd = new CustomerData(1, "name", "adress", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(CustomerReaderRepository::class)->method('getCustomerById')->with('1')->willReturn($cd);

        $service = $this->container->get(CustomerReader::class);

        $actual = $service->getCustomerDetails(1);

        static::assertSame($cd, $actual);
    }

    public function testGetCustomerDetailsKoEmptyParam()
    {
        $this->expectException(ValidationException::class);

        $cd = new CustomerData(1, "name", "adress", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(CustomerReaderRepository::class)->method('getCustomerById')->withAnyParameters()->willReturn($cd);

        $service = $this->container->get(CustomerReader::class);

        $actual = $service->getCustomerDetails(null);

        static::assertSame("Customer ID required", $this->getExpectedExceptionMessage());

    }
}
