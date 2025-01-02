<?php

namespace App\Repositories;

use App\Models\User;

interface IUserRepository {
    public function get(string $uuid): ?User;
    public function save(User $user): void;
}

?>
