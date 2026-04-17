<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'school_year_id',
        'quarter',
        'ww_total_score',
        'ww_max_score',
        'ww_percentage',
        'ww_weighted',
        'pt_total_score',
        'pt_max_score',
        'pt_percentage',
        'pt_weighted',
        'qa_score',
        'qa_max_score',
        'qa_percentage',
        'qa_weighted',
        'quarterly_grade',
        'remarks',
        'status',
        'submitted_at',
        'approved_by',
        'approved_at',
        'client_id',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'ww_total_score' => 'decimal:2',
            'ww_max_score' => 'decimal:2',
            'ww_percentage' => 'decimal:2',
            'ww_weighted' => 'decimal:2',
            'pt_total_score' => 'decimal:2',
            'pt_max_score' => 'decimal:2',
            'pt_percentage' => 'decimal:2',
            'pt_weighted' => 'decimal:2',
            'qa_score' => 'decimal:2',
            'qa_max_score' => 'decimal:2',
            'qa_percentage' => 'decimal:2',
            'qa_weighted' => 'decimal:2',
            'quarterly_grade' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeForQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeForStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->whereHas('student', fn ($q) => $q->where('grade_level', $gradeLevel));
    }

    public function scopeCurrentSchoolYear($query)
    {
        return $query->whereHas('schoolYear', fn ($q) => $q->where('is_current', true));
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }
}
