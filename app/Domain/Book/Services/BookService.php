<?php

declare(strict_types=1);

namespace App\Domain\Book\Services;

use App\Application\Book\Jobs\UpdateAuthorLastBookJob;
use App\Domain\Book\DataTransferObjects\BookData;
use App\Domain\Book\Factories\BookFactory;
use App\Domain\Book\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    public function __construct(
        private readonly BookFactory $bookFactory,
    ) {}

    /** @return LengthAwarePaginator<int, Book> */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Book::with('authors')->paginate($perPage);
    }

    public function findOrFail(int $id): Book
    {
        return Book::with('authors')->findOrFail($id);
    }

    public function create(BookData $data): Book
    {
        $book = $this->bookFactory->create($data);

        UpdateAuthorLastBookJob::dispatch($book);

        return $book;
    }

    public function update(Book $book, BookData $data): Book
    {
        return $this->bookFactory->update($book, $data);
    }

    public function delete(Book $book): void
    {
        $book->delete();
    }
}
