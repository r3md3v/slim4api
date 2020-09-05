<?php

namespace App\Domain\Customer\Data;

final class CustomerData
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $address;

    /** @var string */
    public $city;

    /** @var string */
    public $phone;

    /** @var string */
    public $email;

    /**
     * CustomerData constructor.
     * @param int $id
     * @param string $name
     * @param string $address
     * @param string $city
     * @param string $phone
     * @param string $email
     */
    public function __construct(int $id = 1, string $name = "", string $address = "", string $city = "", string $phone = "", string $email = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->phone = $phone;
        $this->email = $email;
    }

    /** @var string */
    // public $updatedby;

    /** @var timestamp */
    // public $updatedat;

}