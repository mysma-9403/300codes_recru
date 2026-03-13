<?php

use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lista autorów z książkami i paginacja', function (): void {
    $authors = Author::factory(20)->create();
    $book = Book::factory()->create();
    $book->authors()->attach($authors->first());

    $response = $this->getJson('/api/authors')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'full_name', 'name', 'surname', 'books']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);

    expect($response->json('meta.total'))->toBe(20);
    expect(count($response->json('data')))->toBe(15);

    $this->getJson('/api/authors?page=2')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('szczegóły autora z jego książkami', function (): void {
    $author = Author::factory()->create(['name' => 'jan', 'surname' => 'kowalski']);
    $books = Book::factory(3)->create();
    $author->books()->attach($books->pluck('id'));

    $this->getJson("/api/authors/{$author->id}")
        ->assertOk()
        ->assertJsonPath('data.full_name', 'Jan Kowalski')
        ->assertJsonPath('data.name', 'Jan')
        ->assertJsonPath('data.surname', 'Kowalski')
        ->assertJsonCount(3, 'data.books');
});

it('filtr search zwraca autorów po tytułach książek', function (): void {
    $authorWithPotter = Author::factory()->create();
    $authorWithoutPotter = Author::factory()->create();

    $potterBook = Book::factory()->create(['title' => 'Harry Potter']);
    $otherBook = Book::factory()->create(['title' => 'Władca Pierścieni']);

    $authorWithPotter->books()->attach($potterBook);
    $authorWithoutPotter->books()->attach($otherBook);

    $response = $this->getJson('/api/authors?search=potter')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->toArray();

    expect($ids)->toContain($authorWithPotter->id);
    expect($ids)->not->toContain($authorWithoutPotter->id);
});

it('filtr search bez wyników zwraca pustą listę', function (): void {
    Author::factory(3)->create();

    $this->getJson('/api/authors?search=nieistniejacytytul')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('szczegóły nieistniejącego autora zwracają 404', function (): void {
    $this->getJson('/api/authors/999')
        ->assertStatus(404);
});
