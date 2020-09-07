<?php

namespace Tests\TestCase\Domain\User\Service;

use App\Domain\User\Data\UserData;
use App\Domain\User\Repository\UserListerRepository;
use App\Domain\User\Service\UserLister;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;
use TypeError;

class UserListerTest extends TestCase
{

    use AppTestTrait;

    public function testGetUserCount()
    {

        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('countUsers')->willReturn(15);

        $service = $this->container->get(UserLister::class);

        $actual = $service->getUserCount();

        static::assertSame(15, $actual);


    }

    public function testGetUserListNominalOK()
    {
        $users = [
            new UserData(1, 'john.doe',
                'John',
                'Doe',
                'john.doe@example.com',
                'user'),
            new UserData(2, 'john2.doe2',
                'John2',
                'Doe2',
                'john2.doe2@example.com',
                'user2'
            )];

        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('getUsers')->withAnyParameters()->willReturn($users);
        $service = $this->container->get(UserLister::class);
        $actual = $service->getUserList(1, 1);
        static::assertEquals($users, $actual);
    }

    /*
     * When size > pagesize then size = pagesize
     */
    public function testGetUserListSizeMaxToDefault()
    {
        $users = [
            new UserData(1, 'john.doe',
                'John',
                'Doe',
                'john.doe@example.com',
                'user')];

        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('getUsers')->with(1, 5)->willReturn($users);
        $service = $this->container->get(UserLister::class);
        $actual = $service->getUserList(1, 100);
        static::assertEquals($users, $actual);
    }

    /*
     * When page < 1 then page = 1
     */
    public function testGetUserListPageInf1ToDefault()
    {
        $users = [
            new UserData(1, 'john.doe',
                'John',
                'Doe',
                'john.doe@example.com',
                'user')];

        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('getUsers')->with(1, 1)->willReturn($users);
        $service = $this->container->get(UserLister::class);
        $actual = $service->getUserList(-1, 1);
        static::assertEquals($users, $actual);
    }

    /*
     * When page is not a number then TypeError exception is sent
     */
    public function testGetUserListPageNotANumber()
    {
        $this->expectException(TypeError::class);
        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('getUsers')->with(1, 5)->willReturn();
        $service = $this->container->get(UserLister::class);
        $actual = $service->getUserList('a', 1);
    }

    /*
     * When size is not a number then TypeError exception is sent
     */
    public function testGetUserListPSizeNotANumber()
    {
        $this->expectException(TypeError::class);
        // Mock the required repository method
        $this->mock(UserListerRepository::class)->method('getUsers')->with(1, 5)->willReturn();
        $service = $this->container->get(UserLister::class);
        $actual = $service->getUserList(1, 'a');
    }
}
