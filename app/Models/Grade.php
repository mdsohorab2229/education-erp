<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_name',
        'grade_letter',
        'min_mark',
        'max_mark',
        'gpa_point',
        'remarks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'min_mark' => 'decimal:2',
            'max_mark' => 'decimal:2',
            'gpa_point' => 'decimal:2',
        ];
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
