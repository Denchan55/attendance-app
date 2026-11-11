<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AttendanceController extends Controller
{
    /**
     * 打刻画面を表示する
     */
    public function create(Request $request)
    {
        $now = now()->locale('ja');

        return view('user.attendance-register', [
            'user' => $request->user(),
            'formattedDate' => $now->isoFormat('YYYY年M月D日(ddd)'),
            'formattedTime' => $now->format('H:i'),
        ]);
    }
    /**
 * 打刻を処理する
 */
public function store(Request $request): RedirectResponse
{
    $user = $request->user();
    $status = $user->attendance_status;

    if ($request->action === 'clock_in' && $status === '勤務外') {
        $user->attendanceRecords()->create([
            'date' => today()->toDateString(),
            'clock_in' => now()->format('H:i:s'),
        ]);
    }

    if ($request->action === 'break_in' && $status === '出勤中') {
        $this->todayRecord($user)->breaks()->create([
            'break_in' => now()->format('H:i:s'),
        ]);
    }

    if ($request->action === 'break_out' && $status === '休憩中') {
        $this->todayRecord($user)->breaks()
            ->whereNull('break_out')
            ->latest('id')
            ->first()
            ->update(['break_out' => now()->format('H:i:s')]);
    }

    if ($request->action === 'clock_out' && $status === '出勤中') {
        $this->todayRecord($user)->update([
            'clock_out' => now()->format('H:i:s'),
        ]);
    }

    return redirect('/attendance');
}

/**
 * 今日の勤怠を取得する
 */
private function todayRecord(User $user): AttendanceRecord
{
    return $user->attendanceRecords()
        ->whereDate('date', today())
        ->firstOrFail();
}
}