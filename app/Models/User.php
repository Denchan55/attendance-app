<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'admin_status',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }
/**
 * 今日の打刻ステータス(勤務外・出勤中・休憩中・退勤済)
 */
public function getAttendanceStatusAttribute(): string
{
    $record = $this->attendanceRecords()
        ->whereDate('date', today())
        ->first();

    if (! $record) {
        return '勤務外';
    }

    if ($record->clock_out) {
        return '退勤済';
    }

    if ($record->breaks()->whereNull('break_out')->exists()) {
        return '休憩中';
    }

    return '出勤中';
}
}
