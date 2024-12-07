<?php

$pdo = new PDO('sqlite:database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = file_get_contents('init.sql');
$pdo->exec($sql);

echo "База данных инициализирована.\n";