<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimeSlotManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder provides utilities for managing time slots
     */
    public function run(): void
    {
        $this->command->info('Time Slot Manager - Choose an option:');
        $this->command->info('1. Reset all time slots');
        $this->command->info('2. Add holiday schedules');
        $this->command->info('3. Add emergency schedules');
        $this->command->info('4. View current statistics');

        $choice = $this->command->ask('Enter your choice (1-4)', '4');

        switch ($choice) {
            case '1':
                $this->resetTimeSlots();
                break;
            case '2':
                $this->addHolidaySchedules();
                break;
            case '3':
                $this->addEmergencySchedules();
                break;
            case '4':
            default:
                $this->showStatistics();
                break;
        }
    }

    /**
     * Reset all time slots and recreate them
     */
    private function resetTimeSlots(): void
    {
        $this->command->info('Resetting all time slots...');

        EmployeeSchedule::truncate();

        // Re-run the TimeSlotSeeder
        $timeSlotSeeder = new TimeSlotSeeder();
        $timeSlotSeeder->setCommand($this->command);
        $timeSlotSeeder->run();

        $this->command->info('Time slots have been reset successfully!');
    }

    /**
     * Add special holiday schedules
     */
    private function addHolidaySchedules(): void
    {
        $this->command->info('Adding holiday schedules...');

        // Get doctors for emergency coverage during holidays
        $doctors = User::whereIn('email', ['doctor@email.com', 'dr.santos@email.com'])->get();

        foreach ($doctors as $doctor) {
            // Add Sunday emergency hours for holidays
            EmployeeSchedule::create([
                'employee_id' => $doctor->id,
                'day_of_week' => 7, // Sunday
                'start_time' => '10:00',
                'end_time' => '14:00',
            ]);
        }

        $this->command->info('Holiday schedules added successfully!');
    }

    /**
     * Add emergency schedules for 24/7 coverage
     */
    private function addEmergencySchedules(): void
    {
        $this->command->info('Adding emergency schedules...');

        // Get nurses for night shifts
        $nurses = User::where('email', 'like', '%nurse%')->get();

        foreach ($nurses as $nurse) {
            // Add night shift coverage (11 PM - 7 AM)
            for ($day = 1; $day <= 7; $day++) {
                EmployeeSchedule::create([
                    'employee_id' => $nurse->id,
                    'day_of_week' => $day,
                    'start_time' => '23:00',
                    'end_time' => '23:59',
                ]);

                EmployeeSchedule::create([
                    'employee_id' => $nurse->id,
                    'day_of_week' => $day,
                    'start_time' => '00:00',
                    'end_time' => '07:00',
                ]);
            }
        }

        $this->command->info('Emergency schedules added successfully!');
    }

    /**
     * Show current time slot statistics
     */
    private function showStatistics(): void
    {
        $this->command->info('=== Time Slot Statistics ===');

        $totalSlots = EmployeeSchedule::count();
        $totalEmployees = User::whereHas('schedules')->count();

        $this->command->info("Total time slots: {$totalSlots}");
        $this->command->info("Employees with schedules: {$totalEmployees}");
        $this->command->info('');

        // Show breakdown by employee
        $employees = User::whereHas('schedules')->with('schedules')->get();

        $this->command->info('=== Employee Schedule Breakdown ===');
        foreach ($employees as $employee) {
            $slotCount = $employee->schedules->count();
            $this->command->info("{$employee->name}: {$slotCount} time slots");

            // Show days covered
            $days = $employee->schedules->pluck('day_of_week')->unique()->sort();
            $dayNames = $days->map(function($day) {
                return ['', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$day];
            })->implode(', ');

            $this->command->info("  Days: {$dayNames}");

            // Show time range
            $earliestStart = $employee->schedules->min('start_time');
            $latestEnd = $employee->schedules->max('end_time');
            $this->command->info("  Time range: {$earliestStart} - {$latestEnd}");
            $this->command->info('');
        }

        // Show coverage by day
        $this->command->info('=== Daily Coverage ===');
        for ($day = 1; $day <= 7; $day++) {
            $dayName = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][$day];
            $daySlots = EmployeeSchedule::where('day_of_week', $day)->count();
            $dayEmployees = EmployeeSchedule::where('day_of_week', $day)->distinct('employee_id')->count();

            $this->command->info("{$dayName}: {$daySlots} slots, {$dayEmployees} employees");
        }
    }

    /**
     * Validate time slot conflicts
     */
    public function validateTimeSlots(): array
    {
        $conflicts = [];

        // Check for overlapping time slots for the same employee on the same day
        $employees = User::whereHas('schedules')->get();

        foreach ($employees as $employee) {
            for ($day = 1; $day <= 7; $day++) {
                $daySchedules = $employee->schedules()
                    ->where('day_of_week', $day)
                    ->orderBy('start_time')
                    ->get();

                for ($i = 0; $i < $daySchedules->count() - 1; $i++) {
                    $current = $daySchedules[$i];
                    $next = $daySchedules[$i + 1];

                    if ($current->end_time > $next->start_time) {
                        $conflicts[] = [
                            'employee' => $employee->name,
                            'day' => $day,
                            'conflict' => "Overlap between {$current->start_time}-{$current->end_time} and {$next->start_time}-{$next->end_time}"
                        ];
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Generate availability report
     */
    public function generateAvailabilityReport(): array
    {
        $report = [];

        for ($day = 1; $day <= 7; $day++) {
            $dayName = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][$day];

            $daySchedules = EmployeeSchedule::where('day_of_week', $day)
                ->orderBy('start_time')
                ->with('employee')
                ->get();

            $report[$dayName] = [
                'total_slots' => $daySchedules->count(),
                'employees' => $daySchedules->pluck('employee.name')->unique()->values()->toArray(),
                'earliest_start' => $daySchedules->min('start_time'),
                'latest_end' => $daySchedules->max('end_time'),
                'coverage_hours' => $this->calculateCoverageHours($daySchedules)
            ];
        }

        return $report;
    }

    /**
     * Calculate total coverage hours for a day
     */
    private function calculateCoverageHours($schedules): float
    {
        $totalMinutes = 0;

        foreach ($schedules as $schedule) {
            $start = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $end = Carbon::createFromFormat('H:i:s', $schedule->end_time);
            $totalMinutes += $start->diffInMinutes($end);
        }

        return round($totalMinutes / 60, 2);
    }
}
