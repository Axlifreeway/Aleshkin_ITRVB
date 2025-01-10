<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\PostRepository;
use App\Models\Post;
use Ramsey\Uuid\Uuid;
use App\Logging\FileLogger;

class DeletePostTest extends TestCase {
    private PDO $pdo;
    private PostRepository $postsRepository;

    protected function setUp(): void {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $logger = new FileLogger('debug.log');

        $this->postsRepository = new PostRepository($this->pdo, $logger);

        $post = new Post(Uuid::uuid4()->toString(), '53106969-d5b7-4156-a425-886a805977f8', 'Test Title', 'Test Content');
        $this->postsRepository->save($post);
    }

    public function testDeletePostSuccess(): void {
        $response = $this->simulateDeleteRequest('/Aleshkin_ITRVB/api.php/posts?uuid=7ec2ad88-9455-45b0-9976-cf147acb6f34');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Post deleted', $response['body']['message']);
    }

    public function testDeletePostInvalidUuid(): void {
        $response = $this->simulateDeleteRequest('/Aleshkin_ITRVB/api.php/posts?uuid=invalid-uuid');

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Invalid or missing UUID', $response['body']['error']);
    }

    public function testDeletePostNotFound(): void {
        $response = $this->simulateDeleteRequest('/Aleshkin_ITRVB/api.php/posts?uuid=7ec2ad88-9455-45b0-9976-cf147acb6f32');

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Post not found', $response['body']['error']);
    }

    private function simulateDeleteRequest(string $url): array {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        ob_start();
        require 'api.php';
        $output = ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => json_decode($output, true),
        ];
    }
}