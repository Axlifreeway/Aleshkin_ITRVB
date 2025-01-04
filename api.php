<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Comment;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use Ramsey\Uuid\Uuid;

$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

global $mockInput;
$input = json_decode(isset($mockInput) ? $mockInput : file_get_contents('php://input'), true);

$commentsRepository = new CommentsRepository($pdo);
$postsRepository = new PostRepository($pdo);

file_put_contents('debug.log', json_encode($input) . PHP_EOL, FILE_APPEND);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/posts/comment') {
    $response = handlePostComment($input, $postsRepository, $commentsRepository);
    http_response_code($response['status']);
    header('Content-Type: application/json');
    echo json_encode($response['body']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && str_starts_with($_SERVER['REQUEST_URI'], '/posts')) {
    header('Content-Type: application/json');
    $response = handleDeletePost($_SERVER['REQUEST_URI'], $postsRepository, $pdo);
    http_response_code($response['status']);
    echo json_encode($response['body']);
}

function handlePostComment($input, $postsRepository, $commentsRepository) {
    if (!isset($input['author_uuid'], $input['post_uuid'], $input['text'])) {
        return [
            'status' => 400,
            'body' => ['error' => 'Missing required fields']
        ];
    }

    if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $input['author_uuid']) ||
        !preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $input['post_uuid'])) {
        return [
            'status' => 400,
            'body' => ['error' => 'Invalid UUID format']
        ];
    }

    try {
        $post = $postsRepository->get($input['post_uuid']);
    } catch (Exception $e) {
        return [
            'status' => 404,
            'body' => ['error' => 'Post not found']
        ];
    }

    $comment = new Comment(Uuid::uuid4()->toString(), $input['post_uuid'], $input['author_uuid'], $input['text']);
    $commentsRepository->save($comment);

    return [
        'status' => 201,
        'body' => ['message' => 'Comment created']
    ];
}

function handleDeletePost($requestUri, $postsRepository, $pdo) {
    $query = [];
    parse_str(parse_url($requestUri, PHP_URL_QUERY), $query);

    if (!isset($query['uuid']) || !preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $query['uuid'])) {
        return [
            'status' => 400,
            'body' => ['error' => 'Invalid or missing UUID']
        ];
    }

    try {
        $post = $postsRepository->get($query['uuid']);

        $stmt = $pdo->prepare("DELETE FROM posts WHERE uuid = :uuid");
        $stmt->execute(['uuid' => $query['uuid']]);

        return [
            'status' => 200,
            'body' => ['message' => 'Post deleted']
        ];
    } catch (Exception $e) {
        return [
            'status' => 404,
            'body' => ['error' => 'Post not found']
        ];
    }
}
