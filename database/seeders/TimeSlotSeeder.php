<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates detailed time slots for appointment booking
     */
    public function run(): void
    {
        // Clear existing schedules to avoid duplicates
        EmployeeSchedule::truncate();

        // Get all employees
        $employees = User::whereIn('id', [3, 4, 5])->get(); // Admin, Employee, Doctor

        foreach ($employees as $employee) {
            $this->createDetailedTimeSlots($employee);
        }

        // Create additional employees with specialized schedules
        $this->createSpecializedEmployees();
    }

    /**
     * Create detailed time slots for each employee
     */
    private function createDetailedTimeSlots(User $employee): void
    {
        $scheduleConfig = $this->getEmployeeScheduleConfig($employee);

        foreach ($scheduleConfig['days'] as $dayConfig) {
            // Create time slots based on appointment duration
            $timeSlots = $this->generateTimeSlots(
                $dayConfig['start'],
                $dayConfig['end'],
                $scheduleConfig['slot_duration'],
                $scheduleConfig['break_times'] ?? []
            );

            foreach ($timeSlots as $slot) {
                EmployeeSchedule::create([
                    'employee_id' => $employee->id,
                    'day_of_week' => $dayConfig['day'],
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ]);
            }
        }
    }

    /**
     * Get schedule configuration for each employee type
     */
    private function getEmployeeScheduleConfig(User $employee): array
    {
        $configs = [
            'admin@email.com' => [
                'slot_duration' => 60, // 1 hour slots for admin tasks
                'days' => [
                    ['day' => 1, 'start' => '07:30', 'end' => '17:30'], // Monday
                    ['day' => 2, 'start' => '07:30', 'end' => '17:30'], // Tuesday
                    ['day' => 3, 'start' => '07:30', 'end' => '17:30'], // Wednesday
                    ['day' => 4, 'start' => '07:30', 'end' => '17:30'], // Thursday
                    ['day' => 5, 'start' => '07:30', 'end' => '17:30'], // Friday
                    ['day' => 6, 'start' => '08:00', 'end' => '13:00'], // Saturday
                ],
                'break_times' => [
                    ['start' => '12:00', 'end' => '13:00'], // Lunch break
                ]
            ],
            'doctor@email.com' => [
                'slot_duration' => 30, // 30-minute appointment slots
                'days' => [
                    ['day' => 1, 'start' => '08:00', 'end' => '18:00'], // Monday
                    ['day' => 2, 'start' => '08:00', 'end' => '18:00'], // Tuesday
                    ['day' => 3, 'start' => '08:00', 'end' => '18:00'], // Wednesday
                    ['day' => 4, 'start' => '08:00', 'end' => '18:00'], // Thursday
                    ['day' => 5, 'start' => '08:00', 'end' => '18:00'], // Friday
                    ['day' => 6, 'start' => '08:00', 'end' => '14:00'], // Saturday
                    ['day' => 7, 'start' => '09:00', 'end' => '12:00'], // Sunday (emergency)
                ],
                'break_times' => [
                    ['start' => '12:00', 'end' => '13:00'], // Lunch break
                    ['start' => '15:00', 'end' => '15:15'], // Afternoon break
                ]
            ],
            'employee@email.com' => [
                'slot_duration' => 45, // 45-minute slots for general services
                'days' => [
                    ['day' => 1, 'start' => '08:00', 'end' => '17:00'], // Monday
                    ['day' => 2, 'start' => '08:00', 'end' => '17:00'], // Tuesday
                    ['day' => 3, 'start' => '08:00', 'end' => '17:00'], // Wednesday
                    ['day' => 4, 'start' => '08:00', 'end' => '17:00'], // Thursday
                    ['day' => 5, 'start' => '08:00', 'end' => '17:00'], // Friday
                    ['day' => 6, 'start' => '08:00', 'end' => '12:00'], // Saturday
                ],
                'break_times' => [
                    ['start' => '12:00', 'end' => '13:00'], // Lunch break
                ]
            ]
        ];

        return $configs[$employee->email] ?? $configs['employee@email.com'];
    }

    /**
     * Generate time slots for a given time range
     */
    private function generateTimeSlots(string $startTime, string $endTime, int $duration, array $breakTimes = []): array
    {
        $slots = [];
        $current = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($duration);

            // Check if this slot conflicts with break times
            $isBreakTime = false;
            foreach ($breakTimes as $breakTime) {
                $breakStart = Carbon::createFromFormat('H:i', $breakTime['start']);
                $breakEnd = Carbon::createFromFormat('H:i', $breakTime['end']);

                if ($slotStart->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                    $isBreakTime = true;
                    break;
                }
            }

            if (!$isBreakTime) {
                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                ];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Create specialized employees with specific schedules
     */
    private function createSpecializedEmployees(): void
    {
        $specializedEmployees = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'dr.santos@email.com',
                'password' => bcrypt('doctor123'),
                'specialty' => 'General Medicine',
                'schedule' => [
                    'slot_duration' => 20, // 20-minute consultations
                    'days' => [
                        ['day' => 1, 'start' => '09:00', 'end' => '17:00'],
                        ['day' => 2, 'start' => '09:00', 'end' => '17:00'],
                        ['day' => 3, 'start' => '09:00', 'end' => '17:00'],
                        ['day' => 4, 'start' => '09:00', 'end' => '17:00'],
                        ['day' => 5, 'start' => '09:00', 'end' => '17:00'],
                    ],
                    'break_times' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => '15:30', 'end' => '15:45'],
                    ]
                ]
            ],
            [
                'name' => 'Nurse Jennifer',
                'email' => 'nurse.jennifer@email.com',
                'password' => bcrypt('nurse123'),
                'specialty' => 'Nursing Care',
                'schedule' => [
                    'slot_duration' => 15, // 15-minute nursing procedures
                    'days' => [
                        ['day' => 1, 'start' => '07:00', 'end' => '15:00'],
                        ['day' => 2, 'start' => '15:00', 'end' => '23:00'],
                        ['day' => 3, 'start' => '07:00', 'end' => '15:00'],
                        ['day' => 4, 'start' => '15:00', 'end' => '23:00'],
                        ['day' => 5, 'start' => '07:00', 'end' => '15:00'],
                        ['day' => 6, 'start' => '08:00', 'end' => '16:00'],
                    ],
                    'break_times' => [
                        ['start' => '11:00', 'end' => '11:30'],
                        ['start' => '19:00', 'end' => '19:30'],
                    ]
                ]
            ],
            [
                'name' => 'Therapist Mark',
                'email' => 'therapist.mark@email.com',
                'password' => bcrypt('therapy123'),
                'specialty' => 'Physical Therapy',
                'schedule' => [
                    'slot_duration' => 60, // 1-hour therapy sessions
                    'days' => [
                        ['day' => 1, 'start' => '08:00', 'end' => '16:00'],
                        ['day' => 2, 'start' => '08:00', 'end' => '16:00'],
                        ['day' => 3, 'start' => '08:00', 'end' => '16:00'],
                        ['day' => 4, 'start' => '08:00', 'end' => '16:00'],
                        ['day' => 5, 'start' => '08:00', 'end' => '16:00'],
                    ],
                    'break_times' => [
                        ['start' => '12:00', 'end' => '13:00'],
                    ]
                ]
            ]
        ];

        foreach ($specializedEmployees as $empData) {
            // Create user
            $user = User::create([
                'name' => $empData['name'],
                'email' => $empData['email'],
                'password' => $empData['password'],
                'email_verified_at' => now(),
            ]);

            // Create time slots for this specialized employee
            foreach ($empData['schedule']['days'] as $dayConfig) {
                $timeSlots = $this->generateTimeSlots(
                    $dayConfig['start'],
                    $dayConfig['end'],
                    $empData['schedule']['slot_duration'],
                    $empData['schedule']['break_times'] ?? []
                );

                foreach ($timeSlots as $slot) {
                    EmployeeSchedule::create([
                        'employee_id' => $user->id,
                        'day_of_week' => $dayConfig['day'],
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                    ]);
                }
            }
        }
    }
}
