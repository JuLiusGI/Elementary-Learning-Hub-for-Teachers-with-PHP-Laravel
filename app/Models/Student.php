<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'lrn',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'gender',
        'address_street',
        'address_barangay',
        'address_municipality',
        'address_province',
        'guardian_name',
        'guardian_contact',
        'guardian_relationship',
        'special_needs',
        'medical_notes',
        'photo_path',
        'grade_level',
        'school_year_id',
        'teacher_id',
        'enrollment_status',
        'date_enrolled',
        'previous_school',
        'transfer_date',
        'transfer_reason',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_enrolled' => 'date',
            'transfer_date' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->last_name . ', ' . $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return $name;
    }

    public function getGradeLevelLabelAttribute(): string
    {
        return config('school.grade_levels')[$this->grade_level] ?? $this->grade_level;
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function kinderAssessments(): HasMany
    {
        return $this->hasMany(KinderAssessment::class);
    }

    public function assignmentScores(): HasMany
    {
        return $this->hasMany(AssignmentScore::class);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    public function scopeActive($query)
    {
        return $query->where('enrollment_status', 'active');
    }

    public function scopeCurrentSchoolYear($query)
    {
        return $query->whereHas('schoolYear', fn ($q) => $q->where('is_current', true));
    }
}
