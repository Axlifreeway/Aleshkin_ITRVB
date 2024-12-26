<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Comment;
use App\Models\Like;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use App\Repositories\LikeRepository;
use App\Repositories\TokenRepository;
use App\Auth\TokenService;
use App\Logging\FileLogger;
use App\Logging\ILoggerInterface;
use Ramsey\Uuid\Uuid;

$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$input = json_decode(file_get_contents('php://input'), true);

$logger = new FileLogger(__DIR__ . '/logs/app.log');

$commentsRepository = new CommentsRepository($pdo, $logger);
$postsRepository = new PostRepository($pdo, $logger);
$likeRepository = new LikeRepository($pdo, $logger);

file_put_contents('debug.log', json_encode($input) . PHP_EOL, FILE_APPEND);

// Запрос для комментариев

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/posts/comment') {
    $authHeader = getallheaders()['Authorization'] ?? null;
    if (!$authHeader || !preg_match('/Bearer (.+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
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

// Запрос для статей

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

// Запрос для лайков

if ($_SERVER['REQUEST_METHOD'] === 'POST' && preg_match('/\/likes\/add$/', $_SERVER['REQUEST_URI'])) {
    $authHeader = getallheaders()['Authorization'] ?? null;
    if (!$authHeader || !preg_match('/Bearer (.+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['entity_uuid'], $input['user_uuid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    if (!preg_match('/^[a-f0-9\-]{36}$/i', $input['entity_uuid']) || !preg_match('/^[a-f0-9\-]{36}$/i', $input['user_uuid'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid UUID format']);
        exit;
    }

    try {
        $existingLikes = $likeRepository->getByEntityUuid($input['entity_uuid']);
        foreach ($existingLikes as $like) {
            if ($like['user_uuid'] === $input['user_uuid']) {
                http_response_code(400);
                echo json_encode(['error' => 'User has already liked this entity']);
                exit;
            }
        }

        $like = new Like(Uuid::uuid4()->toString(), $input['entity_uuid'], $input['user_uuid']);
        $likeRepository->save($like);

        http_response_code(201);
        echo json_encode(['message' => 'Like added successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add like']);
    }

    exit;
}
