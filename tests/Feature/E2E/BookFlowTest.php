<?php

use App\Domain\Author\Models\Author;
use App\Domain\Book\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('pełny cykl życia książki: tworzenie → odczyt → aktualizacja → usunięcie', function (): void {
    $user = User::factory()->create();
    $authors = Author::factory(2)->create();

    $token = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()->json('token');

    $headers = ['Authorization' => "Bearer {$token}"];

    $bookData = [
        'title' => 'harry potter i kamień filozoficzny',
        'description' => 'Pierwsza część serii',
        'isbn' => '9781234567890',
        'published_year' => 1997,
        'author_ids' => $authors->pluck('id')->toArray(),
    ];

    $createResponse = $this->postJson('/api/books', $bookData, $headers)
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'Harry Potter I Kamień Filozoficzny')
        ->assertJsonCount(2, 'data.authors');

    $bookId = $createResponse->json('data.id');

    $this->getJson("/api/books/{$bookId}")
        ->assertOk()
        ->assertJsonPath('data.id', $bookId)
        ->assertJsonPath('data.isbn', '9781234567890')
        ->assertJsonCount(2, 'data.authors');

    $this->putJson("/api/books/{$bookId}", [
        'title' => 'harry potter i komnata tajemnic',
        'isbn' => '9781234567891',
    ], $headers)
        ->assertOk()
        ->assertJsonPath('data.title', 'Harry Potter I Komnata Tajemnic')
        ->assertJsonPath('data.isbn', '9781234567891');

    $this->getJson("/api/books/{$bookId}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Harry Potter I Komnata Tajemnic');

    $this->deleteJson("/api/books/{$bookId}", [], $headers)
        ->assertStatus(204);

    $this->getJson("/api/books/{$bookId}")
        ->assertStatus(404);

    $this->assertDatabaseMissing('books', ['id' => $bookId]);
    $this->assertDatabaseMissing('author_book', ['book_id' => $bookId]);
});

it('lista książek jest paginowana', function (): void {
    $author = Author::factory()->create();

    Book::factory(20)->create()->each(function ($book) use ($author): void {
        $book->authors()->attach($author);
    });

    $response = $this->getJson('/api/books')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'title', 'isbn', 'authors']],
            'links',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);

    expect($response->json('meta.total'))->toBe(20);
    expect($response->json('meta.per_page'))->toBe(15);
    expect(count($response->json('data')))->toBe(15);

    $this->getJson('/api/books?page=2')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});
