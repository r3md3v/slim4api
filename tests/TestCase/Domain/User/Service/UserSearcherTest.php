<?php

namespace Tests\TestCase\Domain\User\Service;

use App\Domain\User\Data\UserData;
use App\Domain\User\Repository\UserSearcherRepository;
use App\Domain\User\Service\UserSearcher;
use App\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Tests\AppTestTrait;
use function PHPUnit\Framework\once;

class UserSearcherTest extends TestCase
{

    use AppTestTrait;

    public function testGetUserSearchOkFound()
    {
        $user = new UserData(1, 'john.doe',
            'fname',
            'lname',
            'email',
            'userprofile');
        $users = [
            $user
        ];

        $this->mock(UserSearcherRepository::class)->method('getUsers')
            ->with('testOk', 'USRNAME', 1, 1)->willReturn($users);
        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch("testOk", '1', 1, 1);

        static::assertSame($users, $actual);
    }

    public function testGetUserSearchOkNobody(): void
    {
        $keyword = 'testOkNobody';
        $in = 'USRNAME';
        $page = 1;
        $pagemax = 1;
        $msgExpected = sprintf('No customer with keyword [%s] in field [%s] page %d / %d!', str_replace('%', '', $keyword), $in, $page + 1, $pagemax);

        $this->mock(UserSearcherRepository::class)->expects(self::once())-> method("getUsers")
            ->with($keyword, $in ,$page , $pagemax)->willThrowException(new DomainException($msgExpected));

        $service = $this->container->get(UserSearcher::class);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($msgExpected);
        $actual= $service->getUserSearch($keyword, '1', $page, $pagemax);

    }

    public function testGetUserSearchKoKeywordIsEmpty(): void
    {

        $this->expectException(ValidationException::class);

        $this->mock(UserSearcherRepository::class)->method("getUsers")
            ->with('', -1, 1, 1);

        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch('', '1', 1, 1);
        $msg = $this->getExpectedExceptionMessage();

        static::assertEquals('Keyword required', $msg);

    }

    public function testGetUserSearchKoPageIsNotNumeric()
    {

        $users = [
            new UserData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(UserSearcherRepository::class)->expects(once())->method("getUsers")
            ->with('testPage', '', 1, 1)->willReturn($users);
        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch('testPage', '', 'a', 1);

        static::assertSame($users, $actual);

    }

    public function testGetUserSearchKoPageIsZero()
    {

        $users = [
            new UserData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(UserSearcherRepository::class)->method("getUsers")
            ->with('testPage', '', 1, 1)->willReturn($users);
        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch('testPage', "", 0, 1);

        static::assertSame($users, $actual);

    }

    public function testGetUserSearchKoPSizeIsNotNumeric()
    {

        $users = [
            new UserData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(UserSearcherRepository::class)->method("getUsers")
            ->with('testSize', '', 1, 5)->willReturn($users)->willReturn($users);
        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch('testSize', '', 1, "a");

        static::assertSame($users, $actual);

    }

    public function testGetUserSearchKoPSizeIsTooMuch()
    {
        $users = [
            new UserData(1, 'john.doe',
                'address',
                'city',
                'phone',
                'email')];

        $this->mock(UserSearcherRepository::class)->method("getUsers")
            ->with('testSize', '', 1, 5)->willReturn($users)->willReturn($users);
        $service = $this->container->get(UserSearcher::class);
        $actual = $service->getUserSearch('testSize', "a", 1, 90000);

        static::assertSame($users, $actual);
    }
}
