<?php

namespace App\Repositories;

use App\Models\Like;
use PDO;
use App\Logging\ILoggerInterface;
use Exception;

class LikeRepository implements ILikeRepository {
    private PDO $db;
    private ILoggerInterface $logger;

    public function __construct(PDO $db, ILoggerInterface $logger) {
        $this->db = $db;
        $this->logger = $logger;
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

        $this->logger->info("Сохранён лайк с UUID: {$like->uuid}");
    }

    public function getByEntityUuid(string $entityUuid): array {
        $stmt = $this->db->prepare('SELECT * FROM likes WHERE entity_uuid = :entity_uuid');
        $stmt->execute(['entity_uuid' => $entityUuid]);

        if (empty($result)) {
            $this->logger->warning("Не было найденных лайков для статьи или комментария с UUID: $entityUuid");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}