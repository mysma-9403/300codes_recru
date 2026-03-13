<?php

declare(strict_types=1);

namespace App\Domain\Author\Factories;

use App\Domain\Author\DataTransferObjects\AuthorData;
use App\Domain\Author\Models\Author;

class AuthorFactory
{
    public function create(AuthorData $data): Author
    {
        return Author::create([
            'name' => $data->name,
            'surname' => $data->surname,
        ]);
    }
}
