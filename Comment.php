<?php
class Comment {
    public string $uuid;
    public string $postUuid;
    public string $authorUuid;
    public string $text;

    public function __construct($uuid, $postUuid, $authorUuid, $text) {
        $this->uuid = $uuid;
        $this->postUuid = $postUuid;
        $this->authorUuid = $authorUuid;
        $this->text = $text;
    }
}

?>
