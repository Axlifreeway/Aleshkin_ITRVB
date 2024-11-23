<?php
namespace App;

class Article {
    public $id;
    public $authorId;
    public $title;
    public $content;

    public function __construct($id, $authorId, $title, $content) {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->content = $content;
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }
}
