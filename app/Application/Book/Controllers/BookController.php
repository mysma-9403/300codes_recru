<?php

declare(strict_types=1);

namespace App\Application\Book\Controllers;

use App\Application\Book\Requests\StoreBookRequest;
use App\Application\Book\Requests\UpdateBookRequest;
use App\Application\Book\Resources\BookResource;
use App\Domain\Book\DataTransferObjects\BookData;
use App\Domain\Book\Services\BookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection($this->bookService->getAll());
    }

    public function show(int $id): BookResource
    {
        return new BookResource($this->bookService->findOrFail($id));
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->create(BookData::fromArray($request->validated()));

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, int $id): BookResource
    {
        $book = $this->bookService->findOrFail($id);

        return new BookResource(
            $this->bookService->update($book, BookData::fromArray(
                array_merge(
                    [
                        'title' => $book->title,
                        'isbn' => $book->isbn,
                        'description' => $book->description,
                        'published_year' => $book->published_year,
                    ],
                    $request->validated()
                )
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $book = $this->bookService->findOrFail($id);
        $this->bookService->delete($book);

        return response()->json(null, 204);
    }
}
