<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'grade_level',
        'school_year_id',
        'quarter',
        'title',
        'description',
        'type',
        'max_score',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssignmentScore::class);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    public function scopeForQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeCurrentSchoolYear($query)
    {
        return $query->whereHas('schoolYear', fn ($q) => $q->where('is_current', true));
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function isWrittenWork(): bool
    {
        return $this->type === 'written_work';
    }

    public function isPerformanceTask(): bool
    {
        return $this->type === 'performance_task';
    }

    public function scoredCount(): int
    {
        return $this->scores()->whereNotNull('score')->count();
    }

    public function averageScore(): ?float
    {
        $avg = $this->scores()->whereNotNull('score')->avg('score');
        return $avg !== null ? round((float) $avg, 2) : null;
    }
}
