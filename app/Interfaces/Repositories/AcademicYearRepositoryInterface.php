<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;

interface AcademicYearRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?AcademicYear;

    public function create(array $data): AcademicYear;

    public function update(int $id, array $data): AcademicYear;

    public function delete(int $id): bool;
}
