<?php

namespace App\Repositories;

use App\Models\Comment;

interface ICommentRepository {
    public function get(string $uuid): Comment;
    public function save(Comment $comment): void;
}
?>