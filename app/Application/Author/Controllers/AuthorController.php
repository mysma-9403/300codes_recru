<?php

declare(strict_types=1);

namespace App\Application\Author\Controllers;

use App\Application\Author\Resources\AuthorResource;
use App\Domain\Author\Services\AuthorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    public function __construct(
        private readonly AuthorService $authorService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return AuthorResource::collection(
            $this->authorService->getAll($request->query())
        );
    }

    public function show(int $id): AuthorResource
    {
        return new AuthorResource($this->authorService->findOrFail($id));
    }
}
