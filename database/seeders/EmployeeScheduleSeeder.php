<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get employees who have employee profiles
        $employees = User::whereHas('employeeProfile')->with('employeeProfile')->get();

        foreach ($employees as $employee) {
            $this->createScheduleForEmployee($employee);
        }
    }

    /**
     * Create schedule for a specific employee based on their role/position
     */
    private function createScheduleForEmployee(User $employee): void
    {
        // Define different schedule patterns based on employee type
        $schedules = $this->getSchedulePattern($employee);

        foreach ($schedules as $schedule) {
            EmployeeSchedule::create([
                'employee_id' => $employee->id,
                'day_of_week' => $schedule['day'],
                'start_time' => $schedule['start'],
                'end_time' => $schedule['end'],
            ]);
        }
    }

    /**
     * Get schedule pattern based on employee
     */
    private function getSchedulePattern(User $employee): array
    {
        // Default clinic hours: 8:00 AM - 5:00 PM, Monday to Saturday
        $standardSchedule = [
            ['day' => 1, 'start' => '08:00', 'end' => '17:00'], // Monday
            ['day' => 2, 'start' => '08:00', 'end' => '17:00'], // Tuesday
            ['day' => 3, 'start' => '08:00', 'end' => '17:00'], // Wednesday
            ['day' => 4, 'start' => '08:00', 'end' => '17:00'], // Thursday
            ['day' => 5, 'start' => '08:00', 'end' => '17:00'], // Friday
            ['day' => 6, 'start' => '08:00', 'end' => '12:00'], // Saturday (half day)
        ];

        // Doctor schedule (longer hours, includes some evening slots)
        $doctorSchedule = [
            ['day' => 1, 'start' => '08:00', 'end' => '18:00'], // Monday
            ['day' => 2, 'start' => '08:00', 'end' => '18:00'], // Tuesday
            ['day' => 3, 'start' => '08:00', 'end' => '18:00'], // Wednesday
            ['day' => 4, 'start' => '08:00', 'end' => '18:00'], // Thursday
            ['day' => 5, 'start' => '08:00', 'end' => '18:00'], // Friday
            ['day' => 6, 'start' => '08:00', 'end' => '14:00'], // Saturday
            ['day' => 7, 'start' => '09:00', 'end' => '12:00'], // Sunday (emergency hours)
        ];

        // Admin schedule (flexible hours)
        $adminSchedule = [
            ['day' => 1, 'start' => '07:30', 'end' => '17:30'], // Monday
            ['day' => 2, 'start' => '07:30', 'end' => '17:30'], // Tuesday
            ['day' => 3, 'start' => '07:30', 'end' => '17:30'], // Wednesday
            ['day' => 4, 'start' => '07:30', 'end' => '17:30'], // Thursday
            ['day' => 5, 'start' => '07:30', 'end' => '17:30'], // Friday
            ['day' => 6, 'start' => '08:00', 'end' => '13:00'], // Saturday
        ];

        // Part-time employee schedule
        $partTimeSchedule = [
            ['day' => 1, 'start' => '09:00', 'end' => '13:00'], // Monday (morning shift)
            ['day' => 3, 'start' => '09:00', 'end' => '13:00'], // Wednesday (morning shift)
            ['day' => 5, 'start' => '09:00', 'end' => '13:00'], // Friday (morning shift)
            ['day' => 2, 'start' => '14:00', 'end' => '17:00'], // Tuesday (afternoon shift)
            ['day' => 4, 'start' => '14:00', 'end' => '17:00'], // Thursday (afternoon shift)
        ];

        // Determine schedule based on employee email/role
        switch ($employee->email) {
            case 'doctor@email.com':
                return $doctorSchedule;
            case 'employee@email.com':
                return $standardSchedule;
            default:
                return $partTimeSchedule;
        }
    }

    /**
     * Create additional employees with schedules for demonstration
     */
    public function createAdditionalEmployees(): void
    {
        // Create additional employees for more comprehensive scheduling
        $additionalEmployees = [
            [
                'name' => 'Nurse Sarah',
                'email' => 'nurse@email.com',
                'password' => bcrypt('nurse123'),
                'schedule_type' => 'nurse'
            ],
            [
                'name' => 'Receptionist Anna',
                'email' => 'receptionist@email.com',
                'password' => bcrypt('reception123'),
                'schedule_type' => 'receptionist'
            ],
            [
                'name' => 'Part-time Assistant',
                'email' => 'assistant@email.com',
                'password' => bcrypt('assistant123'),
                'schedule_type' => 'part_time'
            ]
        ];

        foreach ($additionalEmployees as $empData) {
            $user = User::create([
                'name' => $empData['name'],
                'email' => $empData['email'],
                'password' => $empData['password'],
                'email_verified_at' => now(),
            ]);

            $this->createScheduleForAdditionalEmployee($user, $empData['schedule_type']);
        }
    }

    /**
     * Create schedule for additional employees
     */
    private function createScheduleForAdditionalEmployee(User $employee, string $scheduleType): void
    {
        $schedules = [];

        switch ($scheduleType) {
            case 'nurse':
                // Nurse schedule with shift rotations
                $schedules = [
                    ['day' => 1, 'start' => '06:00', 'end' => '14:00'], // Monday (morning shift)
                    ['day' => 2, 'start' => '14:00', 'end' => '22:00'], // Tuesday (evening shift)
                    ['day' => 3, 'start' => '06:00', 'end' => '14:00'], // Wednesday (morning shift)
                    ['day' => 4, 'start' => '14:00', 'end' => '22:00'], // Thursday (evening shift)
                    ['day' => 5, 'start' => '06:00', 'end' => '14:00'], // Friday (morning shift)
                    ['day' => 6, 'start' => '08:00', 'end' => '16:00'], // Saturday
                ];
                break;

            case 'receptionist':
                // Receptionist schedule (front desk hours)
                $schedules = [
                    ['day' => 1, 'start' => '07:00', 'end' => '16:00'], // Monday
                    ['day' => 2, 'start' => '07:00', 'end' => '16:00'], // Tuesday
                    ['day' => 3, 'start' => '07:00', 'end' => '16:00'], // Wednesday
                    ['day' => 4, 'start' => '07:00', 'end' => '16:00'], // Thursday
                    ['day' => 5, 'start' => '07:00', 'end' => '16:00'], // Friday
                    ['day' => 6, 'start' => '07:00', 'end' => '12:00'], // Saturday
                ];
                break;

            case 'part_time':
                // Part-time assistant schedule
                $schedules = [
                    ['day' => 1, 'start' => '10:00', 'end' => '14:00'], // Monday
                    ['day' => 3, 'start' => '10:00', 'end' => '14:00'], // Wednesday
                    ['day' => 5, 'start' => '10:00', 'end' => '14:00'], // Friday
                ];
                break;
        }

        foreach ($schedules as $schedule) {
            EmployeeSchedule::create([
                'employee_id' => $employee->id,
                'day_of_week' => $schedule['day'],
                'start_time' => $schedule['start'],
                'end_time' => $schedule['end'],
            ]);
        }
    }
}
