<?php

namespace Tests\Domain\Login\Service;

use App\Domain\Login\Data\LoginData;
use App\Domain\Login\Repository\LoginManagerRepository;
use App\Domain\Login\Service\LoginManager;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class LoginManagerTest extends TestCase
{

    use AppTestTrait;

    public function testGetLoginListOk()
    {
        $logins = [
            new LoginData(1, 'john.doe',
                'email',
                'desc',
                'last token',
                'status')];

        $this->mock(LoginManagerRepository::class)->method("getLogins")
            ->with(1, 1)->willReturn($logins);
        $service = $this->container->get(LoginManager::class);
        $actual = $service->getLoginList(1, 1);
        static::assertEquals($logins, $actual);
    }


    public function testGetLoginListOkNoLogin()
    {
        $this->expectException(DomainException::class);
        $this->mock(LoginManagerRepository::class)->method("getLogins")
            ->willThrowException(new DomainException(sprintf('No login!')));
        $service = $this->container->get(LoginManager::class);
        $actual = $service->getLoginList(1, 1);

        $msg = $this->getExpectedExceptionMessage();
        static::assertEquals('No login!', $msg);
    }

    public function testGetLoginListKopageIsNotNumeric()
    {
        $logins = [
            new LoginData(1, 'john.doe',
                'email',
                'desc',
                'last token',
                'status')];

        $this->mock(LoginManagerRepository::class)->method("getLogins")->with(1, 1)
            ->willReturn($logins);
        $service = $this->container->get(LoginManager::class);
        $actual = $service->getLoginList("a", 1);
        static::assertEquals($logins, $actual);
    }

    public function testGetLoginListKoSizeIsNotNumeric()
    {
        $logins = [
            new LoginData(1, 'john.doe',
                'email',
                'desc',
                'last token',
                'status')];

        $this->mock(LoginManagerRepository::class)->method("getLogins")->with(1, 5)
            ->willReturn($logins);
        $service = $this->container->get(LoginManager::class);
        $actual = $service->getLoginList(1, "e");
        static::assertEquals($logins, $actual);
    }


    public function testGetLoginCountOk()
    {
        $this->mock(LoginManagerRepository::class)->method("countLogins")
            ->willReturn(15);
        $service = $this->container->get(LoginManager::class);
        $actual = $service->getLoginCount();
        static::assertEquals(15, $actual);
    }

}
