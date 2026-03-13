<?php

declare(strict_types=1);

namespace App\Domain\Author\Models;

use App\Domain\Book\Models\Book;
use Database\Factories\AuthorFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string|null $last_added_book_title
 * @property-read string $full_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'last_added_book_title',
    ];

    /** @return Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value)),
        );
    }

    /** @return Attribute<string, string> */
    protected function surname(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value)),
        );
    }

    /** @return Attribute<string, never> */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->name} {$this->surname}",
        );
    }

    /** @return BelongsToMany<Book, $this> */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    protected static function newFactory(): AuthorFactory
    {
        return AuthorFactory::new();
    }
}
