<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'scheduled_time',
        'priority',
        'recurrence',
        'custom_days',
        'estimated_duration',
        'is_active',
    ];

    protected $casts = [
        'custom_days' => 'array',
        'is_active' => 'boolean',
        'scheduled_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function streak(): HasOne
    {
        return $this->hasOne(Streak::class);
    }

    public function logForDate(Carbon $date): ?DailyLog
    {
        return $this->dailyLogs()->whereDate('log_date', $date)->first();
    }

    /**
     * Determine whether this task is scheduled to occur on the given date,
     * based on its recurrence rule.
     */
    public function occursOn(Carbon $date): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Don't count days before the task existed
        if ($date->lt($this->created_at->copy()->startOfDay())) {
            return false;
        }

        return match ($this->recurrence) {
            'Daily' => true,
            'Weekdays' => $date->isWeekday(),
            'Weekends' => $date->isWeekend(),
            'Custom' => in_array(strtolower($date->format('l')), $this->custom_days ?? []),
            default => false,
        };
    }
}
