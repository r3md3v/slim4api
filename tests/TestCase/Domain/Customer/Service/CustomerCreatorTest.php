<?php

namespace Tests\TestCase\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerCreatorRepository;
use App\Domain\Customer\Service\CustomerCreator;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class CustomerCreatorTest extends TestCase
{

    use AppTestTrait;

    public function testCreateCustomerOk()
    {
        $customer = [
            'cusname' => 'john.doe',
            'address' => 'address field',
            'city' => 'city field',
            'phone' => 'phone field',
            'email' => 'john.doe@example.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class)->method('customerExists')->with($customer['email'])->willReturn(true);
        $this->mock(CustomerCreatorRepository::class)->method('insertCustomer')->with($customer)->willReturn(1);

        $service = $this->container->get(CustomerCreator::class);
        $actual = $service->createCustomer($customer);

        static::assertSame(1, $actual);

    }

    public function testCreateCustomerKOCustomerExists(): void
    {
        $customer = [
            'cusname' => 'john.doe',
            'address' => 'address field',
            'city' => 'city field',
            'phone' => 'phone field',
            'email' => 'john.doe@example.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class)->method('customerExists')->with($customer['email'])->willReturn(true);
        $service = $this->container->get(CustomerCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Customer already exists with email [' . $customer['email'] . ']');

        $actual = $service->createCustomer($customer);

    }

    public function testCreateCustomerKOFormMissingCusName(): void
    {
        $customer = [
            'cusname' => '',
            'address' => 'address field',
            'city' => 'city field',
            'phone' => 'phone field',
            'email' => 'john.doe@example.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class);
        $service = $this->container->get(CustomerCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createCustomer($customer);

    }

    public function testCreateCustomerKOFormMissingAddress(): void
    {
        $customer = [
            'cusname' => 'john.Doe',
            'address' => '',
            'city' => 'city field',
            'phone' => 'phone field',
            'email' => 'john.doe@example.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class);
        $service = $this->container->get(CustomerCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createCustomer($customer);

    }

    public function testCreateCustomerKOFormMissingCityName(): void
    {
        $customer = [
            'cusname' => 'john.doe',
            'address' => 'address field',
            'city' => '',
            'phone' => 'phone field',
            'email' => 'john.doe@example.com'
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class);
        $service = $this->container->get(CustomerCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createCustomer($customer);

    }

    public function testCreateCustomerOKFormMissingPhone(): void
    {
        $customer = [
            'cusname' => 'john.doe',
            'address' => 'address field',
            'city' => 'city field',
            'phone' => '',
            'email' => 'john.doe@example.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class)->method('customerExists')->with($customer['email'])->willReturn(true);
        $this->mock(CustomerCreatorRepository::class)->method('insertCustomer')->with($customer)->willReturn(1);

        $service = $this->container->get(CustomerCreator::class);


        $actual = $service->createCustomer($customer);
        static::assertSame(1, $actual);

    }


    public function testCreateCustomerKOFormMissingEmail(): void
    {
        $customer = [
            'cusname' => 'john.doe',
            'address' => 'address field',
            'city' => 'city field',
            'phone' => 'phone field',
            'email' => ''
        ];

        // Mock the required repository method
        $this->mock(CustomerCreatorRepository::class);
        $service = $this->container->get(CustomerCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createCustomer($customer);

    }
}
