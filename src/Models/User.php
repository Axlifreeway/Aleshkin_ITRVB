<?php

namespace App\Models;

class User {
    public string $uuid;
    public string $firstName;
    public string $lastName;

    public function __construct($uuid, $firstName, $lastName) {
        $this->uuid = $uuid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}

?>