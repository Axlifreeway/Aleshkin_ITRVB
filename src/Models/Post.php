<?php

namespace App\Models;

class Post {
    public string $uuid;
    public string $authorUuid;
    public string $title;
    public string $text;

    public function __construct($uuid, $authorUuid, $title, $text) {
        $this->uuid = $uuid;
        $this->authorUuid = $authorUuid;
        $this->title = $title;
        $this->text = $text;
    }
}

?>