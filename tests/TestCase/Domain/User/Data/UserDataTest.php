<?php

namespace Tests\TestCase\Domain\User\Data;

use App\Domain\User\Data\UserData;
use PHPUnit\Framework\TestCase;

class UserDataTest extends TestCase
{

    public function test__construct()
    {
        $user = new UserData(1, 'john.doe',
            'John',
            'Doe',
            'john.doe@example.com',
            'user');

        static::assertEquals(1, $user->id);
        static::assertEquals('john.doe', $user->username);
        static::assertEquals('John', $user->firstName);
        static::assertEquals('Doe', $user->lastName);
        static::assertEquals('john.doe@example.com', $user->email);
        static::assertEquals('user', $user->profile);

    }
}
