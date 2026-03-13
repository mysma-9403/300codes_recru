<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Author\DataTransferObjects\AuthorData;
use App\Domain\Author\Factories\AuthorFactory;
use Illuminate\Console\Command;

class CreateAuthorCommand extends Command
{
    protected $signature = 'author:create';

    protected $description = 'Tworzy nowego autora';

    public function handle(AuthorFactory $authorFactory): int
    {
        $name = $this->ask('Podaj imię autora');
        $surname = $this->ask('Podaj nazwisko autora');

        if (! $name || ! $surname) {
            $this->error('Imię i nazwisko są wymagane.');

            return self::FAILURE;
        }

        $data = new AuthorData(name: $name, surname: $surname);
        $author = $authorFactory->create($data);

        $this->info("Autor {$author->full_name} został utworzony (ID: {$author->id}).");

        return self::SUCCESS;
    }
}
