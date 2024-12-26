<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Logging\ILoggerInterface;
use PDO;
use Exception;

class CommentsRepository implements ICommentRepository {
    private PDO $db;
    private ILoggerInterface $logger;

    public function __construct(PDO $db, ILoggerInterface $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get(string $uuid): Comment {
        $stmt = $this->db->prepare('SELECT * FROM comments WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$comment) {
            $this->logger->warning("Не удалось найти комментарий с UUID: $uuid");
        }

        return new Comment($comment['uuid'], $comment['post_uuid'], $comment['author_uuid'], $comment['text']);
    }

    public function save(Comment $comment): void {
        $stmt = $this->db->prepare('
            INSERT INTO comments (uuid, post_uuid, author_uuid, text) 
            VALUES (:uuid, :post_uuid, :author_uuid, :text)
        ');
        $stmt->execute([
            'uuid' => $comment->uuid,
            'post_uuid' => $comment->postUuid,
            'author_uuid' => $comment->authorUuid,
            'text' => $comment->text
        ]);

        $this->logger->info("Сохранён комментарий с UUID: {$comment->uuid}");
    }
}
