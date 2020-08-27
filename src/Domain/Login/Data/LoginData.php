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
    public $token;

    /** @var string */
    public $status;
}