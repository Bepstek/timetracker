<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all student users (exclude admin)
        $students = \App\Models\User::where('role', 'student')->get();

        // Create time logs for the past 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Skip weekends (optional)
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($students as $student) {
                // Random chance to have a time log for this day
                if (rand(1, 100) > 40) {
                    // Random start time between 8:00 and 9:00
                    $timeIn = $date->copy()
                        ->setHours(rand(8, 9))
                        ->setMinutes(rand(0, 59))
                        ->setSeconds(0);

                    // Random end time between 17:00 and 18:00
                    $timeOut = $date->copy()
                        ->setHours(rand(17, 18))
                        ->setMinutes(rand(0, 59))
                        ->setSeconds(0);

                    \App\Models\TimeLog::create([
                        'user_id' => $student->id,
                        'date' => $date->format('Y-m-d'),
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'notes' => collect([
                            'Regular workday',
                            'Completed project tasks',
                            'Attended training session',
                            'Code review and debugging',
                            null,
                        ])->random(),
                        'status' => 'completed',
                    ]);
                }
            }
        }
    }
}
