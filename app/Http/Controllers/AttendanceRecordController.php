<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller
{
    /**
     * 勤怠詳細を表示する
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $record = $user->attendanceRecords()->with('breaks')->findOrFail($id);
        $application = $record->applications()
            ->with('proposalBreaks')
            ->where('approval_status', '承認待ち')
            ->first();

        if ($application) {
            $clockIn = $application->new_clock_in;
            $clockOut = $application->new_clock_out;
            $breaks = $application->proposalBreaks;
            $comment = $application->comment;
        } else {
            $clockIn = $record->clock_in;
            $clockOut = $record->clock_out;
            $breaks = $record->breaks;
            $comment = $record->comment;
        }

        $date = Carbon::parse($record->date);

        $data = [
            'id' => $record->id,
            'year' => $date->format('Y年'),
            'date' => $date->format('n月j日'),
            'clock_in' => $clockIn ? substr($clockIn, 0, 5) : '',
            'clock_out' => $clockOut ? substr($clockOut, 0, 5) : '',
            'breaks' => $breaks->map(fn ($break) => [
                'break_in' => substr($break->break_in, 0, 5),
                'break_out' => $break->break_out ? substr($break->break_out, 0, 5) : '',
            ])->all(),
            'comment' => $comment,
            'application' => $application,
        ];

        return view('user.user-detail', [
            'user' => $user,
            'data' => $data,
        ]);
    }
}