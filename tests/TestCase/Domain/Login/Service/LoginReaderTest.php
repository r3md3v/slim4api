<?php

namespace Tests\TestCase\Domain\Login\Service;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginRepository;
use App\Domain\Login\Service\LoginManager;
use App\Domain\Login\Service\Tools;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class LoginReaderTest extends TestCase
{

    use AppTestTrait;

    public function testGetLoginDetailsOkStatus1()
    {
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', '1');
        $this->mock(LoginRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $this->mock(Tools::class)->method('getUserIpAddr')->willReturn('127.0.0.1');
        $service = $this->container->get(LoginManager::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip', true);
        self::assertEquals($login, $actual);
    }

    public function testGetLoginDetailsOkStatus0()
    {
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', '0');
        $this->mock(LoginRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $this->mock(Tools::class)->method('getUserIpAddr')->willReturn('127.0.0.1');
        $service = $this->container->get(LoginManager::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip', true);
        self::assertEquals($login, $actual);
    }

    public function testGetLoginDetailsKoEmptyUsername()
    {
        $this->expectException(ValidationException::class);
        $this->mock(LoginRepository::class)->method('getLoginByUMP')
            ->with('', 'password')
            ->willThrowException(new ValidationException('Username and password required'));
        $this->mock(Tools::class)->method('getUserIpAddr')->willReturn('127.0.0.1');

        $service = $this->container->get(LoginManager::class);
        $service->getLoginDetails('', 'password', 'sourceip', true);

        $msg = $this->getExpectedExceptionMessage();
        self::assertEquals('Username and password required', $msg);
    }

    public function testGetLoginDetailsKoEmptyPassword()
    {
        $this->expectException(ValidationException::class);
        $this->mock(LoginRepository::class)->method('getLoginByUMP')
            ->with('username', '')
            ->willThrowException(new ValidationException('Username and password required'));
        $this->mock(Tools::class)->method('getUserIpAddr')->willReturn('127.0.0.1');

        $service = $this->container->get(LoginManager::class);
        $service->getLoginDetails('username', '', 'sourceip', true);

        $msg = $this->getExpectedExceptionMessage();
        self::assertEquals('Username and password required', $msg);
    }

    public function testGetLoginDetailsKoKStatusSup1()
    {
        $this->expectException(ValidationException::class);
        $login = new LoginData(0, "username", 'na', 'na', 'lasttoken', 'Login incorrect for: username');
        $this->mock(LoginRepository::class)->method('getLoginByUMP')
            ->with('username', 'password')->willReturn($login);
        $service = $this->container->get(LoginManager::class);

        $actual = $service->getLoginDetails('username', 'password', 'sourceip', true);
        self::assertEquals($login, $actual);
    }

    public function testCleanupLogins()
    {
        $this->mock(LoginRepository::class)->method('cleanupLogin')
            ->with(25)->willReturn(35);
        $service = $this->container->get(LoginManager::class);
        $actual = $service->cleanupLogins(25);
        static::assertEquals(35, $actual);
    }
}
