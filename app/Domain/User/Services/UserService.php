<?php

declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Models\User;

class UserService
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
