<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'student_id',
        'date',
        'status',
        'time_in',
        'remarks',
        'recorded_by',
        'client_id',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'synced_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->whereHas('student', fn ($q) => $q->where('grade_level', $gradeLevel));
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeCurrentSchoolYear($query)
    {
        return $query->whereHas('student', fn ($q) => $q->currentSchoolYear());
    }
}
