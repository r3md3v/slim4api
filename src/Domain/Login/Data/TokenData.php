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
     * @param int $id
     * @param string $username
     * @param string $token
     * @param string $status
     * @param string $updated
     * @param string $expired
     */
    public function __construct(int $id, string $username, string $token, string $status, string $updated, string $expired)
    {
        $this->id = $id;
        $this->username = $username;
        $this->token = $token;
        $this->status = $status;
        $this->updated = $updated;
        $this->expired = $expired;
    }


}
