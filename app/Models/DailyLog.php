<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'user_id', 'log_date', 'status', 'notes',
        'completed_at', 'skip_reason', 'actual_duration',
    ];

    protected $casts = [
        'log_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}