<?php

class User {
    public string $uuid;
    public string $username;
    public string $firstName;
    public string $lastName;

    public function __construct($uuid, $username, $firstName, $lastName) {
        $this->uuid = $uuid;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}

?>