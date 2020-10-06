<?php

namespace Tests\TestCase\Domain\Customer\Service;

use App\Domain\Customer\Repository\CustomerUpdatorRepository;
use App\Domain\Customer\Service\CustomerUpdator;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

/**
 * @internal
 * @coversNothing
 */
class CustomerUpdatorTest extends TestCase
{
    use AppTestTrait;

    public function testUpdateCustomerOk()
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)->expects($this->once())->method('updateCustomer')->with(1, $customer)->willReturn(true);

        $service = $this->container->get(CustomerUpdator::class);
        $actual = $service->updateCustomer(1, $customer);

        static::assertSame(1, $actual);
    }

    public function testUpdateCustomerKOCustomerExists(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)->method('customerExists')->with(1, $customer['email'])->willReturn(true);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Customer already exists with email ['.$customer['email'].']');

        $service = $this->container->get(CustomerUpdator::class);
        $actual = $service->updateCustomer(1, $customer);
    }

    public function testUpdateCustomerKOFormMissingCusName(): void
    {
        $customer = [
            'cusname' => '',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)->expects($this->never())->method('updateCustomer');
        $service = $this->container->get(CustomerUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateCustomer(1, $customer);
    }

    public function testUpdateCustomerKOFormMissingAddress(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)->expects($this->never())->method('updateCustomer');
        $service = $this->container->get(CustomerUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateCustomer(1, $customer);
    }

    public function testUpdateCustomerKOFormMissingCityName(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '',
            'phone' => '0102030405',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)->expects($this->never())->method('updateCustomer');

        $service = $this->container->get(CustomerUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateCustomer(1, $customer);
    }

    public function testUpdateCustomerOKFormMissingPhone(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '',
            'email' => 'john@doe.com',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class)
            ->expects($this->once())
            ->method('updateCustomer')
            ->with(1, $customer)
            ->willReturn(true)
        ;

        $service = $this->container->get(CustomerUpdator::class);

        $actual = $service->updateCustomer(1, $customer);
        static::assertSame(1, $actual);
    }

    public function testUpdateCustomerKOFormMissingEmail(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => '',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class);

        $service = $this->container->get(CustomerUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateCustomer(1, $customer);
    }

    public function testUpdateCustomerKOFormFormatEmail(): void
    {
        $customer = [
            'cusname' => 'Customer SARL',
            'address' => '25 rue des arbres',
            'city' => '59000 Lille',
            'phone' => '0102030405',
            'email' => 'john@doe',
        ];

        // Mock the required repository method
        $this->mock(CustomerUpdatorRepository::class);
        $service = $this->container->get(CustomerUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateCustomer(1, $customer);
    }
}
