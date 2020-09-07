<?php

namespace App\Domain\Login\Data;

final class LoginData
{
    /**
     * @var int
     */
    public $id;

    /** @var string */
    public $username;

    /** @var string */
    public $email;

    /** @var string */
    public $description;

    /** @var string */
    public $lasttoken;

    /** @var string */
    public $status;

    /**
     * LoginData constructor.
     * @param int $id
     * @param string $username
     * @param string $email
     * @param string $description
     * @param string $lasttoken
     * @param string $status
     */
    public function __construct(int $id, string $username, string $email, string $description, string $lasttoken, string $status)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->description = $description;
        $this->lasttoken = $lasttoken;
        $this->status = $status;
    }


}