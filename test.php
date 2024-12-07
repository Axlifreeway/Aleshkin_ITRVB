<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentsRepository;
use App\Repositories\PostRepository;
use Ramsey\Uuid\Uuid;


$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$commentsRepository = new CommentsRepository($pdo);
$postsRepository = new PostRepository($pdo);

$post = new Post(Uuid::uuid4()->toString(), 'author-uuid-123', 'Заголовок', 'Текст статьи');
$postsRepository->save($post);

$comment = new Comment(Uuid::uuid4()->toString(), $post->uuid, 'author-uuid-456', 'Текст комментария');
$commentsRepository->save($comment);

$retrievedPost = $postsRepository->get($post->uuid);
echo "Статья: {$retrievedPost->title}\n";

$retrievedComment = $commentsRepository->get($comment->uuid);
echo "Комментарий: {$retrievedComment->text}\n";