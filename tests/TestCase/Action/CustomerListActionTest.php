<?php

namespace Tests\TestCase\Action;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerListerRepository;
use Monolog\Test\TestCase;
use Tests\AppTestTrait;

/**
 * @internal
 * @coversNothing
 */
class CustomerListActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @dataProvider provideCustomerListerAction
     *
     * @param CustomerData $customer The user
     * @param array        $expected The expected result
     */
    public function testCustomerListAction(CustomerData $customer, array $expected): void
    {
        // Mock the repository resultset
        $this->mock(CustomerListerRepository::class)->method('getCustomers')->withAnyParameters()->willReturn($expected);

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
    public function provideCustomerListerAction(): array
    {
        $customer = new CustomerData(
            0,
            'Customer SARL',
            '25 rue des arbres',
            '59000 Lille',
            '0102030405',
            'john@doe.com'
        );

        $customer->updatedby = 'slim4api';
        $customer->updatedat = '2020-10-05 04:03:02';

        return [
            'Customer' => [
                $customer,
                [
                    'id' => 0,
                    'name' => 'Customer SARL',
                    'address' => '25 rue des arbres',
                    'city' => '59000 Lille',
                    'phone' => '0102030405',
                    'email' => 'john@doe.com',
                ],
            ],
        ];
    }
}
