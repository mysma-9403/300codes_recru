<?php

declare(strict_types=1);

namespace App\Domain\Author\Filters;

use App\Domain\Author\Models\Author;
use App\Domain\Shared\Filters\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

/*
 * Rejestr filtrów dla encji Author.
 *
 * Dodanie nowego filtra wymaga jedynie:
 * 1. Stworzenia klasy implementującej FilterInterface
 * 2. Dodania jej do tablicy $filters poniżej
 *
 * Alternatywnie można zautomatyzować rejestrację filtrów przez:
 * - tagowane serwisy w kontenerze IoC (ServiceProvider + tag 'author.filters')
 * - auto-discovery klas z katalogu Filters przez refleksję
 * Świadomie wybrano jawną tablicę — jest prostsza, czytelniejsza
 * i łatwiejsza w debugowaniu (KISS).
 */
class AuthorFilterPipeline
{
    /** @var list<class-string<FilterInterface>> */
    private array $filters = [
        SearchFilter::class,
    ];

    /**
     * @param  Builder<Author>  $query
     * @param  array<string, string>  $params
     * @return Builder<Author>
     */
    public function apply(Builder $query, array $params): Builder
    {
        foreach ($this->filters as $filterClass) {
            /** @var FilterInterface $filter */
            $filter = new $filterClass;

            $value = $params[$filter->paramName()] ?? null;

            if ($value !== null && $value !== '') {
                $filter->apply($query, $value);
            }
        }

        return $query;
    }

    /**
     * @param  array<string, string>  $params
     * @return Builder<Author>
     */
    public function buildQuery(array $params): Builder
    {
        return $this->apply(Author::with('books'), $params);
    }
}
