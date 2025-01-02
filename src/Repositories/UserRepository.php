<?php

namespace App\Repositories;

use App\Models\User;
use App\Logging\ILoggerInterface;
use PDO;
use Exception;

class UserRepository implements IUserRepository {
    private PDO $db;
    private ILoggerInterface $logger;

    public function __construct(PDO $db, ILoggerInterface $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get(string $uuid): ?User {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->logger->warning("Не удалось найти пользователя с UUID: $uuid");
            return null;
        }

        return new User($user['uuid'], $user['firstName'], $user['lastName']);
    }

    public function save(User $user): void {
        $stmt = $this->db->prepare('
            INSERT INTO users (uuid, firstName, lastName) 
            VALUES (:uuid, :firstName, :lastName)
        ');
        $stmt->execute([
            'uuid' => $user->uuid,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
        ]);

        $this->logger->info("Сохранён пользователь с UUID: {$user->uuid}");
    }
}

?>
