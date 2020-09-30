<?php

namespace Tests\TestCase\Domain\User\Service;

use App\Domain\User\Data\UserData;
use App\Domain\User\Repository\UserReaderRepository;
use App\Domain\User\Service\UserReader;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class UserReaderTest extends TestCase
{

    use AppTestTrait;

    public function testGetCustomerDetailsOk()
    {
        $cd = new UserData(1, "name", "adress", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(UserReaderRepository::class)->method('getUserById')->with('1')->willReturn($cd);

        $service = $this->container->get(UserReader::class);

        $actual = $service->getUserDetails(1);

        static::assertSame($cd, $actual);
    }

    public function testGetCustomerDetailsKoEmptyParam()
    {

        $cd = new UserData(1, "name", "adress", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(UserReaderRepository::class)->method('getUserById')->withAnyParameters();

        $service = $this->container->get(UserReader::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User ID required');

        $actual = $service->getUserDetails(null);

    }

    public function testGetCustomerDetailsKoCustomerNotFound()
    {

        $cd = new UserData(1, "name", "adress", "city", "phone", "email");
        // Mock the required repository method
        $this->mock(UserReaderRepository::class)->method('getUserById')->withAnyParameters();

        $service = $this->container->get(UserReader::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User ID required');

        $actual = $service->getUserDetails(null);

    }

}
