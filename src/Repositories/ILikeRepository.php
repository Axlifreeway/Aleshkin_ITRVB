<?php

namespace App\Repositories;

use App\Models\Like;

interface ILikeRepository {
    public function save(Like $like): void;
    public function getByEntityUuid(string $entityUuid): array;
}