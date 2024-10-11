<?php
namespace App;

class Comment {
    public $id;
    public $authorId;
    public $articleId;
    public $content;

    public function __construct($id, $authorId, $articleId, $content) {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->articleId = $articleId;
        $this->content = $content;
    }
}
