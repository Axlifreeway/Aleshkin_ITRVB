<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Comment;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use Ramsey\Uuid\Uuid;

$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$input = json_decode(file_get_contents('php://input'), true);

$commentsRepository = new CommentsRepository($pdo);
$postsRepository = new PostRepository($pdo);

file_put_contents('debug.log', json_encode($input) . PHP_EOL, FILE_APPEND);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/posts/comment') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['author_uuid'], $input['post_uuid'], $input['text'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    if (!preg_match('/^[a-f0-9\-]{36}$/i', $input['author_uuid']) || !preg_match('/^[a-f0-9\-]{36}$/i', $input['post_uuid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid UUID format']);
        exit;
    }
    try {
        $post = $postsRepository->get($input['post_uuid']);
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
        exit;
    }
    $comment = new Comment(Uuid::uuid4()->toString(), $input['post_uuid'], $input['author_uuid'], $input['text']);
    $commentsRepository->save($comment);
    http_response_code(201);
    echo json_encode(['message' => 'Comment created']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && str_starts_with($_SERVER['REQUEST_URI'], '/posts')) {
    header('Content-Type: application/json');

    $query = [];
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query);

    if (!isset($query['uuid']) || !preg_match('/^[a-f0-9\-]{36}$/i', $query['uuid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or missing UUID']);
        exit;
    }

    try {
        $post = $postsRepository->get($query['uuid']);

        $stmt = $pdo->prepare("DELETE FROM posts WHERE uuid = :uuid");
        $stmt->execute(['uuid' => $query['uuid']]);

        http_response_code(200);
        echo json_encode(['message' => 'Post deleted']);
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => 'Post not found']);
    }

    exit;
}
