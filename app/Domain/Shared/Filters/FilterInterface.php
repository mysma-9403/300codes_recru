<?php

declare(strict_types=1);

namespace App\Domain\Shared\Filters;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public function paramName(): string;

    /** @phpstan-ignore-next-line */
    public function apply(Builder $query, string $value): void;
}
