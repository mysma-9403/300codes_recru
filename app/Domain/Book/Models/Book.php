<?php

declare(strict_types=1);

namespace App\Domain\Book\Models;

use App\Domain\Author\Models\Author;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $isbn
 * @property int|null $published_year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'isbn',
        'published_year',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'published_year' => 'integer',
        ];
    }

    /** @return Attribute<string, string> */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value)),
        );
    }

    /** @return BelongsToMany<Author, $this> */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    protected static function newFactory(): BookFactory
    {
        return BookFactory::new();
    }
}
