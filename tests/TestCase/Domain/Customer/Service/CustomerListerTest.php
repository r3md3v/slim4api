<?php

namespace Tests\TestCase\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerListerRepository;
use App\Domain\Customer\Service\CustomerLister;
use App\Domain\User\Data\UserData;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;
use TypeError;

class CustomerListerTest extends TestCase
{

    use AppTestTrait;

    public function testgetCustomerCount()
    {

        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('countCustomers')->willReturn(15);

        $service = $this->container->get(CustomerLister::class);

        $actual = $service->getCustomerCount();

        static::assertSame(15, $actual);


    }

    public function testGetUserListNominalOK()
    {
        $users = [
            new CustomerData(1, 'john.doe',
                'address field',
                'city field',
                'phone field',
                'john@doe.org'),
            new CustomerData(2, 'john2.doe2',
                'address2 field',
                'city2 field',
                'phone2 field',
                'john2@doe.org'
            )];

        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->withAnyParameters()->willReturn($users);
        $service = $this->container->get(CustomerLister::class);
        $actual = $service->getCustomerList(1, 1);
        static::assertEquals($users, $actual);
    }

    /*
     * When size > pagesize then size = pagesize
     */
    public function testGetCustomerListSizeMaxToDefault()
    {
        $users = [
            new UserData(1, 'john.doe',
                'John',
                'Doe',
                'john.doe@example.com',
                'user')];

        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->with(1, 5)->willReturn($users);
        $service = $this->container->get(CustomerLister::class);
        $actual = $service->getCustomerList(1, 100);
        static::assertEquals($users, $actual);
    }

    /*
     * When page < 1 then page = 1
     */
    public function testGetCustomerListPageInf1ToDefault()
    {
        $users = [
            new UserData(1, 'john.doe',
                'John',
                'Doe',
                'john.doe@example.com',
                'user')];

        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->with(1, 1)->willReturn($users);
        $service = $this->container->get(CustomerLister::class);
        $actual = $service->getCustomerList(-1, 1);
        static::assertEquals($users, $actual);
    }

    /*
     * When page is not a number then TypeError exception is sent
     */
    public function testGetCustomerListPageNotANumber()
    {
        $this->expectException(TypeError::class);
        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->with(1, 5)->willReturn();
        $service = $this->container->get(CustomerLister::class);
        $actual = $service->getCustomerList('a', 1);
    }

    /*
     * When size is not a number then TypeError exception is sent
     */
    public function testGetCustomerListPSizeNotANumber()
    {
        $this->expectException(TypeError::class);
        // Mock the required repository method
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->with(1, 5)->willReturn();
        $service = $this->container->get(CustomerLister::class);
        $actual = $service->getCustomerList(1, 'a');
    }
}
