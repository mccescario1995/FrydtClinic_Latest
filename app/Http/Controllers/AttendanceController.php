<?php
namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Display the attendance form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('attendance.attendance');
    }

    /**
     * Process the attendance based on PIN and time of entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // 1. Validate the PIN and image data
        $request->validate([
            'pin'        => 'required|exists:users,pin|digits:6',
            'image_data' => 'required',
        ], [
            'pin.required'        => 'Please enter your 6-digit PIN.',
            'pin.digits'          => 'Your PIN must be exactly 6 digits.',
            'pin.exists'          => 'The entered PIN does not match any record.',
            'image_data.required' => 'Please capture or upload an image.',
        ]);

        // 2. Find the employee and their attendance record for the day
        $user = User::where('pin', $request->pin)->where('user_type', 'employee')->first();
        if (!$user) {
            return back()->withErrors(['pin' => 'Invalid PIN or user is not an employee.']);
        }
        $employeeId = $user->id;
        $currentDate     = Carbon::today()->toDateString();

        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $currentDate)
            ->first();

        // dd($attendance);

        $actionType = null;
        $message    = '';
        $success    = false;

        // Decode the image
        $imageData = $request->image_data;
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // dd($attendance);

        // 3. Determine if it's a 'time_in' or 'time_out'
        if (! $attendance) {
            // -------- TIME IN --------
            $actionType = 'time_in';
            $message    = 'Time In successful!';

            $imageName = 'attendance_' . $employeeId . '_' . $currentDate . '_timein.jpeg';
            $imagePath = 'public/images/attendance/' . $imageName;
            Storage::put($imagePath, base64_decode($imageData));
            $imageStoragePath = 'images/attendance/' . $imageName;

            $attendance = EmployeeAttendance::create([
                'employee_id'          => $employeeId,
                'date'                 => $currentDate,
                'check_in_time'        => Carbon::now('Asia/Manila')->format('H:i:s'),
                'image_proof_check_in' => $imageStoragePath,
            ]);

            // Fire clock in event
            event(new \App\Events\EmployeeClockIn($attendance));

            // Fire clock in event
            event(new \App\Events\EmployeeClockIn($attendance));

        } elseif (! $attendance->check_out_time) {
            // -------- TIME OUT --------
            $actionType = 'time_out';
            $message    = 'Time Out successful!';

            $imageName = 'attendance_' . $employeeId . '_' . $currentDate . '_timeout.jpeg';
            $imagePath = 'public/images/attendance/' . $imageName;
            Storage::put($imagePath, base64_decode($imageData));
            $imageStoragePath = 'images/attendance/' . $imageName;

            $attendance->update([
                'check_out_time'        => Carbon::now('Asia/Manila')->format('H:i:s'),
                'image_proof_check_out' => $imageStoragePath,
            ]);

            // Fire clock out event
            event(new \App\Events\EmployeeClockOut($attendance));

            // Fire clock out event
            event(new \App\Events\EmployeeClockOut($attendance));

        } else {
            // -------- ALREADY TIMED OUT --------
            return back()->with('error', 'You have already Timed Out today.');
        }

        return view('attendance.attendance', compact('user', 'attendance', 'message', 'actionType'));
    }

}
