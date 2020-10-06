<?php

namespace Tests\Auth;

use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

class JwtAuthTest extends TestCase
{

    use AppTestTrait;

    public function testCreateJwt()
    {
        $now = time();

        $token = new Token(
            [],
            [
                'iss' => new EqualsTo('iss', 'test'),
                'iat' => new LesserOrEqualsTo('iat', $now),
                'nbf' => new LesserOrEqualsTo('nbf', $now + 20),
                'exp' => new GreaterOrEqualsTo('exp', $now + 500),
                'testing' => new Basic('testing', 'test')
            ]
        );

        $data = new ValidationData($now + 10);

    }

    public function testValidateToken()
    {

    }

    public function testCreateParsedToken()
    {

    }

    public function testDecodeToken()
    {

    }

    public function testGetLifetime()
    {

    }
}
