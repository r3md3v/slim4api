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
     *
     * @param mixed $id
     * @param mixed $username
     * @param mixed $email
     * @param mixed $description
     * @param mixed $lasttoken
     * @param mixed $status
     */
    public function __construct($id, $username, $email, $description, $lasttoken, $status)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->description = $description;
        $this->lasttoken = $lasttoken;
        $this->status = $status;
    }
}
