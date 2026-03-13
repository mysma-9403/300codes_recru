<?php

use App\Application\Book\Jobs\UpdateAuthorLastBookJob;
use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

it('tworzy książkę z poprawnymi danymi', function (): void {
    $user = User::factory()->create();
    $authors = Author::factory(2)->create();

    $payload = [
        'title' => 'Testowa Książka',
        'description' => 'Opis testowy',
        'isbn' => '9781234567890',
        'published_year' => 2024,
        'author_ids' => $authors->pluck('id')->toArray(),
    ];

    $response = $this->actingAs($user)->postJson('/api/books', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'Testowa Książka')
        ->assertJsonPath('data.isbn', '9781234567890')
        ->assertJsonCount(2, 'data.authors');

    $this->assertDatabaseHas('books', ['title' => 'Testowa Książka', 'isbn' => '9781234567890']);
    $this->assertDatabaseCount('author_book', 2);
});

it('zwraca błąd walidacji przy brakujących danych', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/books', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'isbn', 'author_ids']);
});

it('zwraca błąd walidacji przy zduplikowanym ISBN', function (): void {
    $user = User::factory()->create();
    $author = Author::factory()->create();
    Book::factory()->create(['isbn' => '9781234567890']);

    $payload = [
        'title' => 'Inna Książka',
        'isbn' => '9781234567890',
        'author_ids' => [$author->id],
    ];

    $response = $this->actingAs($user)->postJson('/api/books', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['isbn']);
});

it('wymaga uwierzytelnienia do tworzenia książki', function (): void {
    $response = $this->postJson('/api/books', []);

    $response->assertStatus(401);
});

it('dispatchuje job aktualizacji tytułu u autora', function (): void {
    Bus::fake();

    $user = User::factory()->create();
    $author = Author::factory()->create();

    $payload = [
        'title' => 'Nowa Książka',
        'isbn' => '9781234567890',
        'author_ids' => [$author->id],
    ];

    $this->actingAs($user)->postJson('/api/books', $payload);

    Bus::assertDispatched(UpdateAuthorLastBookJob::class);
});
