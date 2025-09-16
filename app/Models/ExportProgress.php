<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_id',
        'user_id',
        'type',
        'percentage',
        'message',
        'status',
        'data',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update progress
     */
    public function updateProgress($percentage, $message, $status = 'processing', $data = [])
    {
        $this->update([
            'percentage' => $percentage,
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'completed_at' => $status === 'success' || $status === 'error' ? now() : null
        ]);
    }

    /**
     * Mark as completed
     */
    public function markCompleted($message = 'Export selesai!', $data = [])
    {
        $this->updateProgress(100, $message, 'success', $data);
    }

    /**
     * Mark as failed
     */
    public function markFailed($message = 'Export gagal!')
    {
        $this->updateProgress(0, $message, 'error');
    }
}