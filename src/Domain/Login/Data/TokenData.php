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

    /**
     * TokenData constructor.
     *
     * @param int    $id
     * @param string $username
     * @param mixed  $token
     * @param mixed  $status
     * @param mixed  $updated
     * @param mixed  $expired
     */
    public function __construct($id, $username, $token, $status, $updated, $expired)
    {
        $this->id = $id;
        $this->username = $username;
        $this->token = $token;
        $this->status = $status;
        $this->updated = $updated;
        $this->expired = $expired;
    }
}
