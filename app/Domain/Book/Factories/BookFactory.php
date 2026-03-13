<?php

declare(strict_types=1);

namespace App\Domain\Book\Factories;

use App\Domain\Book\DataTransferObjects\BookData;
use App\Domain\Book\Models\Book;

class BookFactory
{
    public function create(BookData $data): Book
    {
        $book = Book::create([
            'title' => $data->title,
            'description' => $data->description,
            'isbn' => $data->isbn,
            'published_year' => $data->publishedYear,
        ]);

        $book->authors()->sync($data->authorIds);
        $book->load('authors');

        return $book;
    }

    public function update(Book $book, BookData $data): Book
    {
        $book->update([
            'title' => $data->title,
            'description' => $data->description,
            'isbn' => $data->isbn,
            'published_year' => $data->publishedYear,
        ]);

        if ($data->authorIds !== []) {
            $book->authors()->sync($data->authorIds);
        }

        $book->load('authors');

        return $book;
    }
}
