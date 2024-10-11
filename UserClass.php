<?php

class User {
    protected string $id;
    protected string $username;
    protected string $email;

    public function __construct(string $id, string $username, string $email) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getId(): string {
        return $this->id;
    }
}
