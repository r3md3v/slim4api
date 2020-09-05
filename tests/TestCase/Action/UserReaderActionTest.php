<?php

namespace Tests\TestCase\Action;

use App\Domain\User\Data\UserData;
use App\Domain\User\Repository\UserReaderRepository;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;

/**
 * Test.
 */
class UserReaderActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @dataProvider provideUserReaderAction
     *
     * @param UserData $user The user
     * @param array $expected The expected result
     *
     * @return void
     */
    public function testUserReaderAction(UserData $user, array $expected): void
    {
        // Mock the repository resultset
        $this->mock(UserReaderRepository::class)->method('getUserById')->willReturn($user);

        // Create request with method and url
        $request = $this->createRequest('GET', '/users/1');

        // Make request and fetch response
        $response = $this->app->handle($request);

        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonData($response, $expected);
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideUserReaderAction(): array
    {
        $user = new UserData(1, 'admin', 'john', 'Doe', 'john.doe@example.com', 'user');
        return [
            'User' => [
                $user,
                [
                    'user_id' => 1,
                    'username' => 'admin',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                ]
            ]
        ];
    }
}
