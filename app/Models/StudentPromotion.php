<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPromotion extends Model
{
    protected $fillable = [
        'student_id',
        'from_school_year_id',
        'to_school_year_id',
        'from_grade_level',
        'to_grade_level',
        'general_average',
        'status',
        'decision_by',
        'remarks',
        'promoted_at',
    ];

    protected function casts(): array
    {
        return [
            'general_average' => 'decimal:2',
            'promoted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fromSchoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'from_school_year_id');
    }

    public function toSchoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'to_school_year_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_by');
    }

    public static function nextGradeLevel(string $gradeLevel): string
    {
        $map = [
            'kinder' => 'grade_1',
            'grade_1' => 'grade_2',
            'grade_2' => 'grade_3',
            'grade_3' => 'grade_4',
            'grade_4' => 'grade_5',
            'grade_5' => 'grade_6',
            'grade_6' => 'graduated',
        ];

        return $map[$gradeLevel] ?? $gradeLevel;
    }
}
