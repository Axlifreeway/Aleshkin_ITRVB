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

    public function getId() {
        return $this->id;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function getArticleId() {
        return $this->articleId;
    }

    public function getContent() {
        return $this->content;
    }
}
