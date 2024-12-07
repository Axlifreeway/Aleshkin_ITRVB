<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\CommentsRepository;
use App\Models\Comment;
use Ramsey\Uuid\Uuid;
use PDO;
use Exception;

class CommentsRepositoryTest extends TestCase {
    private PDO $pdo;
    private CommentsRepository $repository;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $this->repository = new CommentsRepository($this->pdo);
    }

    public function testSaveComment(): void {
        $comment = new Comment(Uuid::uuid4()->toString(), 'post-uuid-123', 'author-uuid-123', 'Test Comment');
        $this->repository->save($comment);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM comments");
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testGetCommentByUuid(): void {
        $comment = new Comment(Uuid::uuid4()->toString(), 'post-uuid-123', 'author-uuid-123', 'Test Comment');
        $this->repository->save($comment);

        $retrievedComment = $this->repository->get($comment->uuid);
        $this->assertEquals($comment->uuid, $retrievedComment->uuid);
        $this->assertEquals($comment->text, $retrievedComment->text);
    }

    public function testGetCommentNotFoundThrowsException(): void {
        $this->expectException(Exception::class);
        $this->repository->get(Uuid::uuid4()->toString());
    }
}