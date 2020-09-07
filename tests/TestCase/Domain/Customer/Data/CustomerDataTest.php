<?php

namespace Tests\TestCase\Domain\Customer\Data;

use App\Domain\Customer\Data\CustomerData;
use PHPUnit\Framework\TestCase;

class CustomerDataTest extends TestCase
{

    public function test__construct()
    {
        $user = new CustomerData(1, 'john.doe',
            'address field',
            'city field',
            'phone field',
            'john.doe@example.com');

        static::assertEquals(1, $user->id);
        static::assertEquals('address field', $user->address);
        static::assertEquals('city field', $user->city);
        static::assertEquals('phone field', $user->phone);
        static::assertEquals('john.doe@example.com', $user->email);
    }
}
