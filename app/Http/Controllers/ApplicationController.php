<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * 申請一覧を表示する
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $applications = Application::whereHas('attendanceRecord', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        $formattedApplications = $applications->map(fn ($application) => [
            'id' => $application->id,
            'approval_status' => $application->approval_status,
            'date' => Carbon::parse($application->new_date)->format('Y/m/d'),
            'comment' => $application->comment,
            'application_date' => $application->created_at->format('Y/m/d'),
        ])->all();

        return view('user.user-application-list', [
            'user' => $user,
            'formattedApplications' => $formattedApplications,
        ]);
    }

    /**
     * 申請の勤怠詳細へ移動する
     */
    public function show(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $application = Application::whereHas('attendanceRecord', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        return redirect('/attendance/' . $application->attendance_record_id);
    }
}