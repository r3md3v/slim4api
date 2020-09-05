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
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->withAnyParameters()->willReturn($customer);

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
        $customer = new CustomerData(1, 'admin', '25 rue des arbres', 'Lille',
            "0102030405", 'john.doe@example.com');

        $customer->lastName = 'Doe';
        $customer->updatedby = "roger";
        $customer->updatedat = "123456789";

        return [
            'Customer' => [
                $customer,
                [
                    'user_id' => 1,
                    'username' => 'admin',
                    'name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'city' => 'Lille',
                    'address' => '025 rue des arbres',
                    'phone' => '0102030405'
                ]
            ]
        ];
    }

}
