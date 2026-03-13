<?php

declare(strict_types=1);

namespace App\Domain\Author\Filters;

use App\Domain\Shared\Filters\FilterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SearchFilter implements FilterInterface
{
    public function paramName(): string
    {
        return 'search';
    }

    /** @param Builder<Model> $query */
    public function apply(Builder $query, string $value): void
    {
        $query->whereHas('books', function (Builder $bookQuery) use ($value): void {
            $bookQuery->where('title', 'like', "%{$value}%");
        });
    }
}
