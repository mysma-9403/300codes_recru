<?php

declare(strict_types=1);

namespace App\Application\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isbn' => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'author_ids' => ['required', 'array', 'min:1'],
            'author_ids.*' => ['exists:authors,id'],
        ];
    }
}
