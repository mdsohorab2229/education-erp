<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;

interface SectionRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Section;

    public function create(array $data): Section;

    public function update(int $id, array $data): Section;

    public function delete(int $id): bool;
}
