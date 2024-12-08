<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\PostRepository;
use App\Models\Post;
use Ramsey\Uuid\Uuid;

class PostsRepositoryTest extends TestCase {
    private PDO $pdo;
    private PostRepository $repository;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $this->repository = new PostRepository($this->pdo);
    }

    public function testSavePost(): void {
        $post = new Post(Uuid::uuid4()->toString(), 'author-uuid-123', 'Test Title', 'Test Content');
        $this->repository->save($post);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testGetPostByUuid(): void {
        $post = new Post(Uuid::uuid4()->toString(), 'author-uuid-123', 'Test Title', 'Test Content');
        $this->repository->save($post);

        $retrievedPost = $this->repository->get($post->uuid);
        $this->assertEquals($post->uuid, $retrievedPost->uuid);
        $this->assertEquals($post->title, $retrievedPost->title);
    }

    public function testGetPostNotFoundThrowsException(): void {
        $this->expectException(Exception::class);
        $this->repository->get(Uuid::uuid4()->toString());
    }
}