<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KinderAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_year_id',
        'quarter',
        'domain',
        'rating',
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
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getDomainLabelAttribute(): string
    {
        return config('school.kinder_domains')[$this->domain] ?? $this->domain;
    }

    public function getRatingLabelAttribute(): string
    {
        return config('school.kinder_ratings')[$this->rating] ?? $this->rating;
    }

    public function scopeForQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeForStatus($query, string $status)
    {
        return $query->where('status', $status);
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
