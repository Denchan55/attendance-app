<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'comment',
    ];

    /**
     * この勤怠の持ち主
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この勤怠の休憩(複数)
     */
    public function breaks(): HasMany
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    /**
     * この勤怠への修正申請(複数)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
 * 休憩時間の合計(秒)
 */
public function totalBreakSeconds(): int
{
    $seconds = 0;

    foreach ($this->breaks as $break) {
        if ($break->break_out) {
            $seconds += Carbon::parse($break->break_in)
                ->diffInSeconds(Carbon::parse($break->break_out));
        }
    }

    return $seconds;
}

/**
 * 勤務時間の合計(秒)。退勤前は null
 */
public function totalWorkSeconds(): ?int
{
    if (! $this->clock_out) {
        return null;
    }

    return Carbon::parse($this->clock_in)
        ->diffInSeconds(Carbon::parse($this->clock_out)) - $this->totalBreakSeconds();
}
}