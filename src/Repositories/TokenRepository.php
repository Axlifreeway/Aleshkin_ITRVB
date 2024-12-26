<?php

namespace App\Repositories;

use App\Models\Token;
use PDO;
use DateTime;
use Exception;

class TokenRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function save(Token $token): void {
        $stmt = $this->db->prepare('
            INSERT INTO tokens (value, user_uuid, expires_at) 
            VALUES (:value, :user_uuid, :expires_at)
            ON CONFLICT(value) DO UPDATE SET expires_at = :expires_at
        ');

        $stmt->execute([
            'value' => $token->value,
            'user_uuid' => $token->userUuid,
            'expires_at' => $token->expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function get(string $value): ?Token {
        $stmt = $this->db->prepare('SELECT * FROM tokens WHERE value = :value');
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return new Token(
            $result['value'],
            $result['user_uuid'],
            new DateTime($result['expires_at'])
        );
    }
}