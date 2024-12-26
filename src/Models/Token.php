<?php

namespace App\Models;

use DateTime;

class Token {
    public string $value;
    public string $userUuid;
    public DateTime $expiresAt;

    public function __construct(string $value, string $userUuid, DateTime $expiresAt) {
        $this->value = $value;
        $this->userUuid = $userUuid;
        $this->expiresAt = $expiresAt;
    }

    public function isExpired(): bool {
        return $this->expiresAt < new DateTime();
    }
}