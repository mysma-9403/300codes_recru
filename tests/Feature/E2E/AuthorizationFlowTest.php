<?php

use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('endpointy publiczne działają bez tokenu', function (): void {
    $author = Author::factory()->create();
    $book = Book::factory()->create();
    $book->authors()->attach($author);

    $this->getJson('/api/books')->assertOk();
    $this->getJson("/api/books/{$book->id}")->assertOk();
    $this->getJson('/api/authors')->assertOk();
    $this->getJson("/api/authors/{$author->id}")->assertOk();
});

it('endpointy chronione zwracają 401 bez tokenu', function (): void {
    $book = Book::factory()->create();

    $this->postJson('/api/books', [])->assertStatus(401);
    $this->putJson("/api/books/{$book->id}", [])->assertStatus(401);
    $this->deleteJson("/api/books/{$book->id}")->assertStatus(401);
});

it('nieprawidłowe dane logowania zwracają błąd walidacji', function (): void {
    $this->postJson('/api/login', [
        'email' => 'nie@istnieje.pl',
        'password' => 'zlehaslo',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('nieprawidłowy token zwraca 401', function (): void {
    $this->postJson('/api/books', [], [
        'Authorization' => 'Bearer nieprawidlowy-token',
    ])->assertStatus(401);
});
