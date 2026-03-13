<?php

declare(strict_types=1);

namespace App\Application\Auth\Controllers;

use App\Application\Auth\Requests\LoginRequest;
use App\Application\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->authenticate(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        return response()->json(['token' => $token]);
    }
}
