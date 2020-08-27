<?php

namespace Tests\TestCase\Action;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerListerRepository;
use Monolog\Test\TestCase;
use Tests\AppTestTrait;


class CustomerListActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @dataProvider provideCustomerReaderAction
     *
     * @param CustomerData $customer The user
     * @param array $expected The expected result
     *
     * @return void
     */
    public function testCustomerListAction(CustomerData $customer, array $expected): void
    {
        // Mock the repository resultset
        $this->mock(CustomerListerRepository::class)->method('getCustomers
        ')->willReturn($customer);

        // Create request with method and url
        $request = $this->createRequest('GET', '/customers');

        // Make request and fetch response
        $response = $this->app->handle($request);

        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonData($response, $expected);
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideCustomerReaderAction(): array
    {
        $customer = new CustomerData();
        $customer->id = 1;
        $customer->name = 'admin';
        $customer->address = '025 rue des arbres';
        $customer->email = 'john.doe@example.com';
        $customer->firstName = 'John';
        $customer->lastName = 'Doe';
        $customer->city = 'Lille';
        $customer->phone = "0102030405";
        $customer->updatedby = "roger";
        $customer->updatedat = "123456789";

        return [
            'Customer' => [
                $customer,
                [
                    'user_id' => 1,
                    'username' => 'admin',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'city' => 'Lille',
                    'address' => '025 rue des arbres',
                    'phone' => '0102030405',
                    'updatedby' => 'roger',
                    'updateddat' => "123456789",
                ]
            ]
        ];
    }

}
