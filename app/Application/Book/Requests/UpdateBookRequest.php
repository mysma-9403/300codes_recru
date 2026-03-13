<?php

declare(strict_types=1);

namespace App\Application\Book\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isbn' => ['sometimes', 'string', 'size:13', Rule::unique('books', 'isbn')->ignore($this->route('book'))],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'author_ids' => ['sometimes', 'array', 'min:1'],
            'author_ids.*' => ['exists:authors,id'],
        ];
    }
}
