<?php

namespace App\Models;

class Like {
    public string $uuid;
    public string $entityUuid; //Статья или комментарий
    public string $userUuid;

    public function __construct(string $uuid, string $entityUuid, string $userUuid) {
        $this->uuid = $uuid;
        $this->entityUuid = $entityUuid;
        $this->userUuid = $userUuid;
    }
}