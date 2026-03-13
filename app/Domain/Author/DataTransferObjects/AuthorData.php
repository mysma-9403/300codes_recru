<?php

declare(strict_types=1);

namespace App\Domain\Author\DataTransferObjects;

readonly class AuthorData
{
    public function __construct(
        public string $name,
        public string $surname,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            surname: (string) $data['surname'],
        );
    }
}
