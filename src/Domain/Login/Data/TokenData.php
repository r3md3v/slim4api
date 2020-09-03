<?php

namespace App\Domain\Login\Data;

final class TokenData
{
    /**
     * @var int
     */
    public $id;

    /** @var string */
    public $username;

    /** @var string */
    public $token;

    /** @var string */
    public $status;

    /** @var string */
    public $updated;

    /** @var string */
    public $expired;
}
