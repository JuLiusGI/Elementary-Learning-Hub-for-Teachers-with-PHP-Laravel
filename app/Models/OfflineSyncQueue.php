<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineSyncQueue extends Model
{
    protected $table = 'offline_sync_queue';

    protected $fillable = [
        'user_id',
        'client_id',
        'action',
        'model_type',
        'model_id',
        'payload',
        'client_timestamp',
        'synced_at',
        'sync_status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'client_timestamp' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
