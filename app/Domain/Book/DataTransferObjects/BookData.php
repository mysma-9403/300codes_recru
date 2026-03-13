<?php

declare(strict_types=1);

namespace App\Domain\Book\DataTransferObjects;

readonly class BookData
{
    /**
     * @param  array<int>  $authorIds
     */
    public function __construct(
        public string $title,
        public string $isbn,
        public ?string $description = null,
        public ?int $publishedYear = null,
        public array $authorIds = [],
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            title: (string) $data['title'],
            isbn: (string) $data['isbn'],
            description: $data['description'] ?? null,
            publishedYear: $data['published_year'] ?? null,
            authorIds: array_map('intval', (array) ($data['author_ids'] ?? [])),
        );
    }
}
