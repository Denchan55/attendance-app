<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceRecordSeeder extends Seeder
{

    public function run(): void
    {
    $user = User::all();
    $start = now()->startOfMonth();
    
    foreach ($user as $user) {
        for ($i = 0; $i < $start->daysInMonth; $i++) {
        $date = $start->copy()->addDays($i);
            if ($date->isWeekend()) {
            continue;
        }
        $record = $user->attendanceRecords()->create([
            'date' => $date->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $record->breaks()->create([
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ]);
        } 
    }
}
}