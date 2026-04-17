<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploaded_by',
        'subject_id',
        'grade_level',
        'quarter',
        'week_number',
        'title',
        'description',
        'file_type',
        'file_path',
        'external_url',
        'file_size',
        'mime_type',
        'is_downloadable',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'is_downloadable' => 'boolean',
            'file_size' => 'integer',
            'download_count' => 'integer',
            'week_number' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    public function scopeForQuarter($query, string $quarter)
    {
        return $query->where('quarter', $quarter);
    }

    public function scopeForWeek($query, int $week)
    {
        return $query->where('week_number', $week);
    }

    public function scopeForFileType($query, string $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function isFile(): bool
    {
        return in_array($this->file_type, ['pdf', 'image']);
    }

    public function isLink(): bool
    {
        return in_array($this->file_type, ['video', 'link']);
    }

    public function formattedFileSize(): string
    {
        if (!$this->file_size) {
            return '—';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 1) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }
}
