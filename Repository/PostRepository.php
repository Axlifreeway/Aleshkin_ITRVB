<?php

namespace App\Repositories;

use App\Post;
use PDO;
use Exception;

class PostsRepository implements PostsRepositoryInterface {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function get(string $uuid): Post {
        $stmt = $this->db->prepare('SELECT * FROM posts WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            throw new Exception("Post not found.");
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
    }
}

?>
