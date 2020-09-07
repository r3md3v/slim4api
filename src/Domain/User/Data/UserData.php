<?php

namespace App\Domain\User\Data;

final class UserData
{
    /**
     * @var int
     */
    public $id;

    /** @var string */
    public $username;

    /** @var string */
    //public $password;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var string */
    public $profile;

    /**
     * UserData constructor.
     * @param int $id
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $profile
     */
    public function __construct(int $id = 1, string $username = '', string $firstName = '', string $lastName = '', string $email = '', string $profile = '')
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->profile = $profile;
    }

}