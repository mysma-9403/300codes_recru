<?php

declare(strict_types=1);

namespace App\Application\Book\Jobs;

use App\Domain\Author\Services\AuthorService;
use App\Domain\Book\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAuthorLastBookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Book $book,
    ) {}

    public function handle(AuthorService $authorService): void
    {
        $authorService->updateLastBookTitle($this->book);
    }
}
