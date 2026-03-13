<?php

use App\Domain\Author\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('po dodaniu książki aktualizuje last_added_book_title u autorów', function (): void {
    $user = User::factory()->create();
    $authors = Author::factory(2)->create();

    expect($authors[0]->last_added_book_title)->toBeNull();
    expect($authors[1]->last_added_book_title)->toBeNull();

    $this->actingAs($user)->postJson('/api/books', [
        'title' => 'Nowa Książka',
        'isbn' => '9781234567890',
        'author_ids' => $authors->pluck('id')->toArray(),
    ])->assertStatus(201);

    $authors[0]->refresh();
    $authors[1]->refresh();

    expect($authors[0]->last_added_book_title)->toBe('Nowa Książka');
    expect($authors[1]->last_added_book_title)->toBe('Nowa Książka');
});

it('job aktualizuje tylko autorów przypisanych do książki', function (): void {
    $user = User::factory()->create();
    $assignedAuthor = Author::factory()->create();
    $otherAuthor = Author::factory()->create();

    $this->actingAs($user)->postJson('/api/books', [
        'title' => 'Testowa Książka',
        'isbn' => '9781234567890',
        'author_ids' => [$assignedAuthor->id],
    ])->assertStatus(201);

    $assignedAuthor->refresh();
    $otherAuthor->refresh();

    expect($assignedAuthor->last_added_book_title)->toBe('Testowa Książka');
    expect($otherAuthor->last_added_book_title)->toBeNull();
});
