<?php

require_once ('PHPUnit/Framework/TestCase.php');

use PHPUnit\Framework\TestCase;
use App\Repositories\PostsRepository;
use App\Post;
use PDO;

class SQLitePostsRepositoryTest extends TestCase {
    private PDO $connection;
    private PostsRepository $repository;

    protected function setUp(): void {
        $this->connection = new PDO('sqlite::memory:');
        $this->connection->exec("
            CREATE TABLE posts (
                uuid TEXT PRIMARY KEY,
                author_uuid TEXT NOT NULL,
                title TEXT NOT NULL,
                text TEXT NOT NULL
            );
        ");
        $this->repository = new PostsRepository($this->connection);
    }

    public function testSavePost() {
        $post = new Post('123e4567-e89b-12d3-a456-426614174000', 'author-uuid', 'Test Title', 'Test Text');
        $this->repository->save($post);
        $stmt = $this->connection->query("SELECT COUNT(*) FROM posts");
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testGetPostByUuid() {
        $this->connection->exec("
            INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES ('123e4567-e89b-12d3-a456-426614174000', 'author-uuid', 'Test Title', 'Test Text')
        ");
        $post = $this->repository->get('123e4567-e89b-12d3-a456-426614174000');
        $this->assertEquals('Test Title', $post->title);
    }

    public function testGetPostThrowsExceptionIfNotFound() {
        $this->expectException(Exception::class);
        $this->repository->get('non-existent-uuid');
    }
}