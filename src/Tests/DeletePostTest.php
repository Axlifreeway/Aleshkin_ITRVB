<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use App\Models\Comment;
use App\Models\Post;
use Ramsey\Uuid\Uuid;

class DeletePostTest extends TestCase {
    private PDO $pdo;
    private PostRepository $postsRepository;

    protected function setUp(): void {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $this->postsRepository = new PostRepository($this->pdo);

        $post = new Post('test-post-uuid', 'test-author-uuid', 'Test Title', 'Test Content');
        $this->postsRepository->save($post);
    }

    public function testDeletePostSuccess(): void {
        $response = $this->simulateDeleteRequest('/posts?uuid=test-post-uuid');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Post deleted', $response['body']['message']);
    }

    public function testPostNotFound(): void {
        $response = $this->simulateDeleteRequest('/posts?uuid=nonexistent-uuid');

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Post not found', $response['body']['error']);
    }

    private function simulateDeleteRequest(string $url): array {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = $url;
        ob_start();
        require 'api.php';
        $output = ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => json_decode($output, true),
        ];
    }
}