<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

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

/**
 * 勤怠一覧を表示する
 */
public function index(Request $request)
{
    $date = Carbon::parse($request->query('date', now()->format('Y-m')) . '-01');

    $records = $request->user()->attendanceRecords()
        ->with('breaks')
        ->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
        ->get()
        ->keyBy('date');

    $formattedAttendanceRecords = [];

    for ($i = 0; $i < $date->daysInMonth; $i++) {
        $day = $date->copy()->addDays($i);
        $record = $records->get($day->toDateString());
        $isFinished = $record && $record->clock_out;

        $formattedAttendanceRecords[] = [
            'id' => $record ? $record->id : null,
            'date' => $day->locale('ja')->isoFormat('MM/DD(ddd)'),
            'clock_in' => $record ? substr($record->clock_in, 0, 5) : '',
            'clock_out' => $isFinished ? substr($record->clock_out, 0, 5) : '',
            'total_break_time' => $record ? gmdate('H:i:s', $record->totalBreakSeconds()) : '',
            'total_time' => $isFinished ? gmdate('H:i:s', $record->totalWorkSeconds()) : '',
        ];
    }

    return view('user.user-attendance-list', [
        'date' => $date,
        'previousMonth' => $date->copy()->subMonth()->format('Y-m'),
        'nextMonth' => $date->copy()->addMonth()->format('Y-m'),
        'formattedAttendanceRecords' => $formattedAttendanceRecords,
    ]);
}

}