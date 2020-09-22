<?php

namespace Tests\Domain\User\Service;

use App\Domain\User\Data\UserData;
use App\Domain\User\Repository\UserDeletorRepository;
use App\Domain\User\Service\UserDeletor;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class UserDeletorTest extends TestCase
{
    use AppTestTrait;

    public function testGetUserDetailsKoEmptyParam()
    {
        // Mock the required repository method
        $this->mock(UserDeletorRepository::class)->method('deleteUserById');

        $service = $this->container->get(UserDeletor::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("User ID required");
        $actual = $service->deleteUser(null);

    }

    public function testGetUserDetailsKoNoUserFound()
    {
        $msgExpected= sprintf('User not found: %s', "1");

        // Mock the required repository method
        $this->mock(UserDeletorRepository::class)->method('deleteUserById')
            ->with(1)
            ->willThrowException(new DomainException($msgExpected));

        $service = $this->container->get(UserDeletor::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($msgExpected);
        $actual = $service->deleteUser(1);

    }
}
