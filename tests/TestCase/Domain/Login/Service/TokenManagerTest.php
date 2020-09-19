<?php

namespace Tests\testCase\Domain\Login\Service;

use App\Domain\Login\Data\TokenData;
use App\Domain\Login\Repository\TokenRepository;
use App\Domain\Login\Service\TokenManager;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;
use function PHPUnit\Framework\once;

class TokenManagerTest extends TestCase
{

    use AppTestTrait;

    protected $token;
    protected $tokenData;
    protected $now;

    protected function setUp2(): void
    {
        $this->token = "this.Is.AToken";
        $this->now = date("YYYYMMDDHHMMSS");
        $this->tokenData = new TokenData('1', 'username', $this->token, 'statusOk', $this->now, '-1');
    }

    public function testLogTokenDetails()
    {
        $this->setUp2();
        $username = 'username_';
        $lifetime = 'lifetime';

        $this->mock(TokenRepository::class)->method('insertTokenDetails')->with($username, $this->token, $lifetime)->willReturn(4);

        $service = $this->container->get(TokenManager::class);

        $actual = $service->logTokenDetails($username, $this->token, $lifetime);
        self::assertEquals('4', $actual);
    }

    public function testGetTokenDetailsOK()
    {
        $this->setUp2();
        $this->mock(TokenRepository::class)->method('getTokenByJwt')->with($this->token)->willReturn($this->tokenData);

        $service = $this->container->get(TokenManager::class);

        $actual = $service->getTokenDetails($this->token);
        self::assertEquals($this->tokenData, $actual);

    }

    public function testCleanupTokens()
    {
        $this->setUp2();
        $this->mock(TokenRepository::class)->expects(once())->method('deleteExpiredTokens');

        $service = $this->container->get(TokenManager::class);
        $service->cleanupTokens();
    }

    public function testGetTokenCount()
    {
        $this->setUp2();
        $this->mock(TokenRepository::class)->expects(once())->method('countTokens');

        $service = $this->container->get(TokenManager::class);
        $service->getTokenCount();
    }

    public function testDeleteExpiredTokens()
    {
        $this->setUp2();
        $this->mock(TokenRepository::class)->expects(once())->method('deleteExpiredTokens')->willReturn(5);

        $service = $this->container->get(TokenManager::class);
        $actual = $service->cleanupTokens();
        self::assertEquals('5', $actual);
    }

    public function testRevokeTokenOk()
    {
        $this->setUp2();
        $this->mock(TokenRepository::class)->expects(once())->method('revokeTokenByJwt');

        $service = $this->container->get(TokenManager::class);
        $service->revokeToken($this->token);

    }

    public function testRevokeTokenFailed()
    {
        $this->setUp2();
        $this->expectException(DomainException::class);
        $expectedException= new DomainException(sprintf('Token not found: %s', $this->token));
        $this->mock(TokenRepository::class)
            ->expects(once())
            ->method('revokeTokenByJwt')
            ->willThrowException($expectedException);

        $service = $this->container->get(TokenManager::class);
        $service->revokeToken($this->token);
        $this->expectExceptionMessage($expectedException->getMessage());
    }
}
