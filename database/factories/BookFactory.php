<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Book\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Book> */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'isbn' => fake()->isbn13(),
            'published_year' => fake()->numberBetween(1900, (int) date('Y')),
        ];
    }
}
