<?php

namespace App\Repositories;

use App\Models\Like;
use PDO;
use Exception;

class LikeRepository implements ILikeRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function save(Like $like): void {
        $stmt = $this->db->prepare('
            INSERT INTO likes (uuid, entity_uuid, user_uuid) 
            VALUES (:uuid, :entity_uuid, :user_uuid)
        ');

        $stmt->execute([
            'uuid' => $like->uuid,
            'entity_uuid' => $like->entityUuid,
            'user_uuid' => $like->userUuid
        ]);
    }

    public function getByEntityUuid(string $entityUuid): array {
        $stmt = $this->db->prepare('SELECT * FROM likes WHERE entity_uuid = :entity_uuid');
        $stmt->execute(['entity_uuid' => $entityUuid]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}