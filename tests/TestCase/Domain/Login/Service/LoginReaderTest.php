<?php

namespace Tests\Domain\Login\Services;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginReaderRepository;
use App\Domain\Login\Service\LoginReader;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class LoginReaderTest extends TestCase
{

    use AppTestTrait;

    public function testGetUserIpAddr()
    {
        self::assertEquals('1', '2');
    }

    public function testGetLoginDetailsOKStatus1()
    {
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', '1');
        $this->mock(LoginReaderRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $service = $this->container->get(LoginReader::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip');
        self::assertEquals($login, $actual);
    }

    public function testGetLoginDetailsOKStatus0()
    {
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', '0');
        $this->mock(LoginReaderRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $service = $this->container->get(LoginReader::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip');
        self::assertEquals($login, $actual);
    }

    public function testGetLoginDetailsKOEmptyUsername()
    {
        $this->expectException(ValidationException::class);
        $this->mock(LoginReaderRepository::class)->method('getLoginByUMP')
            ->with('', 'password')
            ->willThrowException(new ValidationException('Username and password required'));
        $service = $this->container->get(LoginReader::class);
        $service->getLoginDetails('', 'password', 'sourceip');
        $msg = $this->getExpectedExceptionMessage();
        self::assertEquals('Username and password required', $msg);
    }

    public function testGetLoginDetailsKOEmptyPassword()
    {
        $this->expectException(ValidationException::class);
        $this->mock(LoginReaderRepository::class)->method('getLoginByUMP')
            ->with('username', '')
            ->willThrowException(new ValidationException('Username and password required'));
        $service = $this->container->get(LoginReader::class);
        $service->getLoginDetails('username', '', 'sourceip');
        $msg = $this->getExpectedExceptionMessage();
        self::assertEquals('Username and password required', $msg);
    }

    public function testGetLoginDetailsKOKStatusSup1()
    {
        $this->expectException(ValidationException::class);
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', 'Login incorrect for: username');
        $this->mock(LoginReaderRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $service = $this->container->get(LoginReader::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip');
        self::assertEquals($login, $actual);
    }

    public function testCleanupLogins()
    {
        $this->mock(LoginReaderRepository::class)->method('cleanupLogin')
            ->with(25)->willReturn(35);
        $service = $this->container->get(LoginReader::class);
        $actual = $service->cleanupLogins(25);
        static::assertEquals(35, $actual);
    }
}
