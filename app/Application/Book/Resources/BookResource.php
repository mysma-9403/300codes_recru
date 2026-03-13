<?php

declare(strict_types=1);

namespace App\Application\Book\Resources;

use App\Application\Author\Resources\AuthorResource;
use App\Domain\Book\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Book */
class BookResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'isbn' => $this->isbn,
            'published_year' => $this->published_year,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
