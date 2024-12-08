<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use App\Models\Post;

class CreateCommentTest extends TestCase {
    private PDO $pdo;
    private CommentsRepository $commentsRepository;
    private PostRepository $postsRepository;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $this->commentsRepository = new CommentsRepository($this->pdo);
        $this->postsRepository = new PostRepository($this->pdo);

        $post = new Post('test-post-uuid', 'test-author-uuid', 'Test Title', 'Test Content');
        $this->postsRepository->save($post);
    }

    public function testCreateCommentSuccess(): void {
        $response = $this->simulatePostRequest('/posts/comment', [
            'author_uuid' => 'test-author-uuid',
            'post_uuid' => 'test-post-uuid',
            'text' => 'Test Comment'
        ]);

        $this->assertEquals(201, $response['status']);
        $this->assertEquals('Comment created', $response['body']['message']);
    }

    public function testInvalidUuidFormat(): void {
        $response = $this->simulatePostRequest('/posts/comment', [
            'author_uuid' => 'invalid-uuid',
            'post_uuid' => 'test-post-uuid',
            'text' => 'Test Comment'
        ]);

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Invalid UUID format', $response['body']['error']);
    }

    public function testPostNotFound(): void {
        $response = $this->simulatePostRequest('/posts/comment', [
            'author_uuid' => 'test-author-uuid',
            'post_uuid' => 'nonexistent-uuid',
            'text' => 'Test Comment'
        ]);

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Post not found', $response['body']['error']);
    }

    public function testMissingFields(): void {
        $response = $this->simulatePostRequest('/posts/comment', [
            'author_uuid' => 'test-author-uuid',
        ]);

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Missing required fields', $response['body']['error']);
    }

    private function simulatePostRequest(string $url, array $data): array {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = $url;
    
        $input = json_encode($data);
        file_put_contents('php://input', $input);
    
        ob_start();
        require 'api.php';
        $output = ob_get_clean();
    
        return [
            'status' => http_response_code(),
            'body' => json_decode($output, true),
        ];
    }
}