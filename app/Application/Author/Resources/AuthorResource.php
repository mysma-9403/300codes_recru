<?php

declare(strict_types=1);

namespace App\Application\Author\Resources;

use App\Application\Book\Resources\BookResource;
use App\Domain\Author\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Author */
class AuthorResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'name' => $this->name,
            'surname' => $this->surname,
            'last_added_book_title' => $this->last_added_book_title,
            'books' => BookResource::collection($this->whenLoaded('books')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
