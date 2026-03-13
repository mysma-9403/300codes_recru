<?php

namespace Database\Seeders;

use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $authors = Author::factory(10)->create();

        Book::factory(20)->create()->each(function (Book $book) use ($authors): void {
            $book->authors()->attach(
                $authors->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
