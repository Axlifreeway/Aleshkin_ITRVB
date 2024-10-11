<?php

use Post;

interface PostsRepositoryInterface {
    public function get(string $uuid): Post;
    public function save(Post $post): void;
}
?>