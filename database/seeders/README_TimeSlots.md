# Employee Time Slot Seeders

This document explains the employee time slot seeders created for the clinic application.

## Available Seeders

### 1. EmployeeScheduleSeeder
Basic seeder that creates standard work schedules for existing employees.

**Usage:**
```bash
php artisan db:seed --class=EmployeeScheduleSeeder
```

### 2. TimeSlotSeeder (Enhanced)
Advanced seeder that creates detailed time slots for appointment booking with realistic clinic conditions.

**Usage:**
```bash
php artisan db:seed --class=TimeSlotSeeder
```

## Clinic Conditions Matched

### Standard Operating Hours
- **Monday to Friday**: 8:00 AM - 5:00 PM
- **Saturday**: 8:00 AM - 12:00 PM (Half day)
- **Sunday**: Limited emergency hours for doctors only

### Employee Types and Schedules

#### 1. Clinic Admin
- **Schedule**: Monday-Friday 7:30 AM - 5:30 PM, Saturday 8:00 AM - 1:00 PM
- **Time Slots**: 60-minute intervals
- **Break Times**: 12:00 PM - 1:00 PM (Lunch)
- **Purpose**: Administrative tasks, management duties

#### 2. Clinic Doctor
- **Schedule**: Monday-Friday 8:00 AM - 6:00 PM, Saturday 8:00 AM - 2:00 PM, Sunday 9:00 AM - 12:00 PM
- **Time Slots**: 30-minute appointment slots
- **Break Times**: 12:00 PM - 1:00 PM (Lunch), 3:00 PM - 3:15 PM (Afternoon break)
- **Purpose**: Patient consultations, medical procedures

#### 3. Clinic Employee (General Staff)
- **Schedule**: Monday-Friday 8:00 AM - 5:00 PM, Saturday 8:00 AM - 12:00 PM
- **Time Slots**: 45-minute service slots
- **Break Times**: 12:00 PM - 1:00 PM (Lunch)
- **Purpose**: General clinic services, patient assistance

#### 4. Specialized Staff (TimeSlotSeeder only)

##### Dr. Maria Santos (General Medicine)
- **Schedule**: Monday-Friday 9:00 AM - 5:00 PM
- **Time Slots**: 20-minute consultation slots
- **Break Times**: 12:00 PM - 1:00 PM, 3:30 PM - 3:45 PM

##### Nurse Jennifer (Nursing Care)
- **Schedule**: Rotating shifts - Morning (7:00 AM - 3:00 PM) and Evening (3:00 PM - 11:00 PM)
- **Time Slots**: 15-minute nursing procedure slots
- **Break Times**: 11:00 AM - 11:30 AM, 7:00 PM - 7:30 PM

##### Therapist Mark (Physical Therapy)
- **Schedule**: Monday-Friday 8:00 AM - 4:00 PM
- **Time Slots**: 60-minute therapy session slots
- **Break Times**: 12:00 PM - 1:00 PM

## Features

### Realistic Clinic Operations
- **Break Time Management**: Automatically excludes break times from available slots
- **Different Slot Durations**: Varies by service type (15-60 minutes)
- **Shift Rotations**: Supports morning, evening, and weekend shifts
- **Emergency Coverage**: Sunday hours for critical services

### Appointment Booking Compatibility
- **Time Slot Granularity**: Precise time slots for appointment scheduling
- **Service-Specific Durations**: Different slot lengths based on service requirements
- **Staff Availability**: Clear visibility of when each staff member is available

### Database Integration
- **Foreign Key Relationships**: Properly linked to users table
- **Cascade Deletion**: Schedules are removed when employees are deleted
- **Timestamp Tracking**: Created and updated timestamps for audit trails

## Usage Examples

### Running Individual Seeders
```bash
# Basic employee schedules
php artisan db:seed --class=EmployeeScheduleSeeder

# Enhanced time slots with specialized staff
php artisan db:seed --class=TimeSlotSeeder
```

### Running All Seeders
```bash
php artisan db:seed
```

### Viewing Seeded Data
```bash
# Using Tinker to view schedules
php artisan tinker
>>> App\Models\EmployeeSchedule::with('employee')->get()->each(function($schedule) { 
    echo $schedule->employee->name . ' - ' . $schedule->day_name . ': ' . 
         $schedule->start_time . ' to ' . $schedule->end_time . PHP_EOL; 
});
```

## Database Structure

### employee_schedules Table
- `id`: Primary key
- `employee_id`: Foreign key to users table
- `day_of_week`: Integer (1=Monday, 7=Sunday)
- `start_time`: Time format (HH:MM)
- `end_time`: Time format (HH:MM)
- `created_at`: Timestamp
- `updated_at`: Timestamp

## Customization

### Adding New Employee Types
1. Create new user in UserSeeder or directly
2. Add schedule configuration in seeder
3. Define appropriate slot duration and break times
4. Run seeder to generate time slots

### Modifying Existing Schedules
1. Update schedule configuration arrays in seeder files
2. Clear existing schedules: `EmployeeSchedule::truncate()`
3. Re-run seeder to apply changes

## Best Practices

1. **Always backup** before running seeders in production
2. **Test schedules** with sample appointments before going live
3. **Consider time zones** when setting up schedules
4. **Regular maintenance** of schedules for holidays and special events
5. **Monitor slot utilization** to optimize scheduling efficiency
