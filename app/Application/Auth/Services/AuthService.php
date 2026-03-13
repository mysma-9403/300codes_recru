<?php

declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\User\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function authenticate(string $email, string $password): string
    {
        $user = $this->userService->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Nieprawidłowe dane logowania.'],
            ]);
        }

        return $user->createToken('api')->plainTextToken;
    }
}
