<?php
require __DIR__ . '/vendor/autoload.php';

use App\User;
use App\Article;

$user = new User(1, 'John', 'Doe');
echo "User: {$user->getFullName()}\n";

$article = new Article(1, $user->getId(), 'Hello World', 'This is a test article.');
echo "Article: {$article->getTitle()} by User ID {$article->getAuthorId()}\n";