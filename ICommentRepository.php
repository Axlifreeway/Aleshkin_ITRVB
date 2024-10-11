<?php

use Comment;

interface CommentsRepositoryInterface {
    public function get(string $uuid): Comment;
    public function save(Comment $comment): void;
}
?>