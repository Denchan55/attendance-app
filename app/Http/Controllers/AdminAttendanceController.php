<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    /**
     * 日次の勤怠一覧を表示する
     */
    public function index(Request $request)
    {
        $date = $this->resolveDate($request->query('date'));

        $users = User::where('admin_status', false)->orderBy('id')->get();

        $attendanceRecords = AttendanceRecord::with('breaks')
            ->whereDate('date', $date)
            ->get();

        return view('admin.admin-attendance-list', [
            'date' => $date,
            'previousDay' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDay' => $date->copy()->addDay()->format('Y-m-d'),
            'users' => $users,
            'attendanceRecords' => $attendanceRecords,
        ]);
    }

    /**
     * ?date= の値を日付に直す。未指定や不正な値なら今日にする
     */
    private function resolveDate(?string $value): Carbon
    {
        if ($value === null) {
            return today();
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable $e) {
            return today();
        }

        return $date->format('Y-m-d') === $value ? $date : today();
    }
}