<?php

namespace Tests\TestCase\Domain\User\Service;

use App\Domain\User\Repository\UserUpdatorRepository;
use App\Domain\User\Service\UserUpdator;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

/**
 * @internal
 * @coversNothing
 */
class UserUpdatorTest extends TestCase
{
    use AppTestTrait;

    public function testUpdateUserOk()
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)
            ->expects($this->once())
            ->method('updateUser')
            ->with(1, $user)
            ->willReturn(true)
        ;

        $service = $this->container->get(UserUpdator::class);
        $actual = $service->updateUser(1, $user);

        static::assertSame(1, $actual);
    }

    public function testUpdateUserKOUserExists(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)
            ->expects($this->once())
            ->method('userExists')
            ->with(1, $user['username'], $user['email'])
            ->willReturn(true)
        ;

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('User already exists with name ['.$user['username'].'] or email ['.$user['email'].']');

        $service = $this->container->get(UserUpdator::class);
        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingUserName(): void
    {
        $user = [
            'username' => '',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');
        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingPassword(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');
        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingFirstName(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');
        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingLastName(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => '',
            'email' => 'john@doe.com',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');

        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingEmail(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => '',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');

        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormMissingProfile(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'profile' => '',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');

        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }

    public function testUpdateUserKOFormFormatEmail(): void
    {
        $user = [
            'username' => 'john.doe',
            'password' => '1234567',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'joh@doe',
            'profile' => 'users customers',
        ];

        // Mock the required repository method
        $this->mock(UserUpdatorRepository::class)->expects($this->never())->method('updateUser');

        $service = $this->container->get(UserUpdator::class);

        $this->expectException(ValidationException::class);
        $this->expectErrorMessage('Please check your input.');

        $actual = $service->updateUser(1, $user);
    }
}
