<?php

namespace App\Repositories;

use App\Models\Post;

interface IPostRepository {
    public function get(string $uuid): Post;
    public function save(Post $post): void;
}
?>