<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    
}