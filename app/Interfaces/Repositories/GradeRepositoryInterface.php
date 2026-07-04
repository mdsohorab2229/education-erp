<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Collection;

interface GradeRepositoryInterface
{
    public function allOrdered(): Collection;
    public function findGradeByMark(float $mark): ?Grade;
    public function create(array $data): Grade;
    public function update(int $id, array $data): Grade;
    public function delete(int $id): bool;
}
