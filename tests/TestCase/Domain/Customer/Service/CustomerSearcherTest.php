<?php

namespace Tests\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerSearcherRepository;
use App\Domain\Customer\Service\CustomerSearcher;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class CustomerSearcherTest extends TestCase
{

    use AppTestTrait;

    public function testGetCustomerSearchOkFound(): void
    {
        $user = new CustomerData(1, 'john.doe',
            'address',
            'city',
            'phone',
            'email');
        $users = [
            $user
        ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testOk', '1', 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch("testOk", 1, 1, 1);

        static::assertSame($users, $actual);

    }

    public function testGetCustomerSearchOkNobody()
    {

        $this->expectException(DomainException::class);


        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('testOkNobody', 1, 1, 1);
        $service = $this->container->get(CustomerSearcher::class);
        $service->getCustomerSearch('testOk', '1', 1, 1);
        $msg = $this->getExpectedExceptionMessage();
        static::assertEquals('No customer!', $msg);

    }

    public function testGetCustomerSearchKoKeywordIsEmpty(): void
    {

        $this->expectException(ValidationException::class);

        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('', -1, 1, 1);

        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('', '1', 1, 1);
        $msg = $this->getExpectedExceptionMessage();

        static::assertEquals('Keyword required', $msg);

    }

    public function testGetCustomerSearchKoPageIsNotNumeric()
    {

        $users = [
            new CustomerData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('testPage', -1, 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testPage', "a", 1, 1);

        static::assertSame($users, $actual);

    }

    public function testGetCustomerSearchKoPageIsZero()
    {

        $users = [
            new CustomerData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('testPage', '-1', 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testPage', "a", 0, 1);

        static::assertSame($users, $actual);

    }

    public function testGetCustomerSearchKoPSizeIsNotNumeric()
    {

        $users = [
            new CustomerData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('testSize', -1, 1, 5)->willReturn($users)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testSize', "a", 1, "a");

        static::assertSame($users, $actual);

    }

    public function testGetCustomerSearchKoPSizeIsTooMuch()
    {
        $users = [
            new CustomerData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(CustomerSearcherRepository::class)->method("getCustomers")
            ->with('testSize', -1, 1, 5)->willReturn($users)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testSize', "a", 1, 90000);

        static::assertSame($users, $actual);
    }

}
