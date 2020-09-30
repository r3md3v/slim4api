<?php

namespace Tests\TestCase\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Repository\CustomerSearcherRepository;
use App\Domain\Customer\Service\CustomerSearcher;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;
use TypeError;

/**
 * @internal
 * @coversNothing
 */
class CustomerSearcherTest extends TestCase
{
    use AppTestTrait;

    public function testGetCustomerSearchOkFound(): void
    {
        $in = ['Customer name', 'CUSNAME'];
        $user = new CustomerData(
            1,
            'john.doe',
            'address',
            'city',
            'phone',
            'email'
        );
        $users = [
            $user,
        ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testOk', $in, 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testOk', '1', 1, 1);

        static::assertSame($users, $actual);
    }

    public function testGetCustomerSearchOkNobody(): void
    {
        $keyword = 'testOkNobody';
        $in = ['Customer name', 'CUSNAME'];
        $page = 1;
        $pagemax = 1;
        $msgExpected = sprintf('No customer with keyword [%s] in field [%s] page %d / %d!', str_replace('%', '', $keyword), $in[0], $page + 1, $pagemax);

        $this->mock(CustomerSearcherRepository::class)->expects(self::once())->method('getCustomers')
            ->with($keyword, $in, $page, $pagemax)->willThrowException(new DomainException($msgExpected));

        $service = $this->container->get(CustomerSearcher::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($msgExpected);
        $actual = $service->getCustomerSearch($keyword, '1', $page, $pagemax);
    }

    public function testGetCustomerSearchKoKeywordIsEmpty(): void
    {
        $this->expectException(ValidationException::class);

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('', [], 1, 1)
        ;

        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('', '1', 1, 1);
        $msg = $this->getExpectedExceptionMessage();

        static::assertEquals('Keyword required', $msg);
    }

    public function testGetCustomerSearchKoPageIsNotNumeric()
    {
        $users = [
            new CustomerData(
                1,
                'john.doe',
                'address',
                'city',
                'phone',
                'email'
            ), ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testPage', [], 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testPage', '', 'a', 1);

        static::assertSame($users, $actual);
    }

    public function testGetCustomerSearchKoPageIsZero()
    {
        $in = ['Customer name', 'CUSNAME'];
        $users = [
            new CustomerData(
                1,
                'john.doe',
                'address',
                'city',
                'phone',
                'email'
            ), ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testPage', [], 1, 1)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testPage', '', 0, 1);

        static::assertSame($users, $actual);
    }

    public function testGetCustomerSearchKoPSizeIsNotNumeric()
    {
        $in = ['Customer name', 'CUSNAME'];
        $users = [
            new CustomerData(
                1,
                'john.doe',
                'address',
                'city',
                'phone',
                'email'
            ), ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testSize', [], 1, 50)->willReturn($users)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testSize', '', 1, 'a');

        static::assertSame($users, $actual);
    }

    public function testGetCustomerSearchKoPSizeIsTooMuch()
    {
        $users = [
            new CustomerData(
                1,
                'john.doe',
                'address',
                'city',
                'phone',
                'email'
            ), ];

        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')
            ->with('testSize', [], 1, 50)->willReturn($users)->willReturn($users);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testSize', '', 1, 90000);

        static::assertSame($users, $actual);
    }

    // When page > maxpage then exception is sent
    public function testGetCustomerSearchPageOverMaxPage()
    {
        $this->expectException(TypeError::class);
        // Mock the required repository method
        $this->mock(CustomerSearcherRepository::class)->method('getCustomers')->with('testPagMax', [], 9999, 50)->willThrowException(TypeError::class);
        $service = $this->container->get(CustomerSearcher::class);
        $actual = $service->getCustomerSearch('testPagMax', '', 9999, 50);
    }
}
