<?php

class MockInputStream {
    private $data;

    public function __construct(string $data) {
        $this->data = $data;
    }

    public function read() {
        return $this->data;
    }
}

use PHPUnit\Framework\TestCase;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use App\Models\Post;
use Ramsey\Uuid\Uuid;
use App\Logging\FileLogger;

class CreateCommentTest extends TestCase {
    private PDO $pdo;
    private CommentsRepository $commentsRepository;
    private PostRepository $postsRepository;
    private MockInputStream $inputStream;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = file_get_contents('init.sql');
        $this->pdo->exec($sql);

        $logger = new FileLogger('debug.log');

        $this->commentsRepository = new CommentsRepository($this->pdo, $logger);
        $this->postsRepository = new PostRepository($this->pdo, $logger);

        $post = new Post(Uuid::uuid4()->toString(), '53106969-d5b7-4156-a425-886a805977f8', 'Test Title', 'Test Content');
        $this->postsRepository->save($post);
    }

    public function testCreateCommentSuccess(): void {
        $response = $this->simulatePostRequest('/Aleshkin_ITRVB/api.php/posts/comment', [
            'author_uuid' => '53106969-d5b7-4156-a425-886a805977f8',
            'post_uuid' => '7ec2ad88-9455-45b0-9976-cf147acb6f34',
            'text' => 'Test Comment'
        ]);

        $this->assertEquals(201, $response['status']);
        $this->assertEquals('Comment created', $response['body']['message']);
    }

    public function testInvalidUuidFormat(): void {
        $response = $this->simulatePostRequest('/Aleshkin_ITRVB/api.php/posts/comment', [
            'author_uuid' => 'invalid-uuid',
            'post_uuid' => 'a7255e67-4bcc-4852-8458-fa54d962e643',
            'text' => 'Test Comment'
        ]);

        $this->assertEquals(400, $response['status']);
        echo($response['body']['error']);
        $this->assertEquals('Invalid UUID format', $response['body']['error']);
    }

    public function testMissingFields(): void {
        $response = $this->simulatePostRequest('/Aleshkin_ITRVB/api.php/posts/comment', [
            'author_uuid' => '53106969-d5b7-4156-a425-886a805977f8',
        ]);

        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Missing required fields', $response['body']['error']);
    }

    private function simulatePostRequest(string $url, array $data): array {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $this->inputStream = new MockInputStream(json_encode($data));

        global $mockInput;
        $mockInput = $this->inputStream->read();

        ob_start();
        require 'api.php';
        $output = ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => json_decode($output, true),
        ];
    }
}