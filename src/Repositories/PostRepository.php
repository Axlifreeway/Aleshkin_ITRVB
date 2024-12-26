<?php

namespace App\Repositories;

use App\Models\Post;
use App\Logging\ILoggerInterface;
use PDO;
use Exception;

class PostRepository implements IPostRepository {
    private PDO $db;
    private ILoggerInterface $logger;

    public function __construct(PDO $db, ILoggerInterface $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function get(string $uuid): Post {
        $stmt = $this->db->prepare('SELECT * FROM posts WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            $this->logger->warning("Не удалось найти статью с UUID: $uuid");
        }

        return new Post($post['uuid'], $post['author_uuid'], $post['title'], $post['text']);
    }

    public function save(Post $post): void {
        $stmt = $this->db->prepare('
            INSERT INTO posts (uuid, author_uuid, title, text) 
            VALUES (:uuid, :author_uuid, :title, :text)
        ');
        $stmt->execute([
            'uuid' => $post->uuid,
            'author_uuid' => $post->authorUuid,
            'title' => $post->title,
            'text' => $post->text
        ]);

        $this->logger->info("Сохранена статья с UUID: {$post->uuid}");
    }
}
?>
