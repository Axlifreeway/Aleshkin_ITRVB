<?php
namespace App;

class User {
    public $id;
    public $firstName;
    public $lastName;

    public function __construct($id, $firstName, $lastName) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getFullName() {
        return $this->firstName. " ". $this->lastName;
    }

    public function getId() {
        return $this->id;
    }
}
