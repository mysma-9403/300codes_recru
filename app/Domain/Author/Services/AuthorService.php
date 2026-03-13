<?php

declare(strict_types=1);

namespace App\Domain\Author\Services;

use App\Domain\Author\Filters\AuthorFilterPipeline;
use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function __construct(
        private readonly AuthorFilterPipeline $filterPipeline,
    ) {}

    /**
     * @param  array<string, string>  $filters
     * @return LengthAwarePaginator<int, Author>
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->filterPipeline
            ->buildQuery($filters)
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Author
    {
        return Author::with('books')->findOrFail($id);
    }

    public function updateLastBookTitle(Book $book): void
    {
        $book->authors()->update([
            'last_added_book_title' => $book->title,
        ]);
    }
}
