<?php

use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('usuwa książkę', function (): void {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $author = Author::factory()->create();
    $book->authors()->attach($author);

    $response = $this->actingAs($user)->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('books', ['id' => $book->id]);
    $this->assertDatabaseMissing('author_book', ['book_id' => $book->id]);
});

it('zwraca 404 dla nieistniejącej książki', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/books/999');

    $response->assertStatus(404);
});

it('wymaga uwierzytelnienia do usuwania książki', function (): void {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(401);
});
