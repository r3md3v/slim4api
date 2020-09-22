<?php

namespace Tests\TestCase\Domain\User\Service;

use App\Domain\User\Repository\UserCreatorRepository;
use App\Domain\User\Service\UserCreator;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

/**
 * Tests.
 */
class UserCreatorTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class)->method('insertUser')->willReturn(1);

        $service = $this->container->get(UserCreator::class);

        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];

        $actual = $service->createUser($user);

        static::assertSame(1, $actual);
    }

    public function testCreateUserKOUserExists(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class)->method('UserExists')->with($user['username'], $user['email'])->willReturn(true);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $msg= 'User already exists with name ['.$user['username'].'] or email ['.$user['email'].']';
        $this->expectErrorMessage($msg);

        $actual = $service->createUser($user);

    }

    public function testCreateUserKOFormMissingUsrName(): void
    {
        $user = [
            'username' => '',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }

    public function testCreateUserKOFormMissingPassword(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }

    public function testCreateUserKOFormMissingFirstName(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }

    public function testCreateUserOKFormMissingLastName(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => '',
            'email' => 'john.doe@example.com',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }


    public function testCreateUserKOFormMissingEmail(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => '',
            'profile' => 'users customers'
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }

    public function testCreateUserKOFormMissingProfile(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'profile' => ''
        ];
        // Mock the required repository method
        $this->mock(UserCreatorRepository::class);
        $service = $this->container->get(UserCreator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->createUser($user);

    }
}
