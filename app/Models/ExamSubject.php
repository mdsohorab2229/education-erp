<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'teacher_id',
        'full_mark',
        'pass_mark',
        'practical_mark',
        'viva_mark',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'full_mark' => 'decimal:2',
            'pass_mark' => 'decimal:2',
            'practical_mark' => 'decimal:2',
            'viva_mark' => 'decimal:2',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
