<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Commands\SeedTestDataCommand;

$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$options = getopt('', ['users-number::', 'posts-number::']);

$command = new SeedTestDataCommand($pdo);
$command->execute($options);
