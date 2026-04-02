<?php

namespace App\Http\Controllers\Auth;

use Backpack\CRUD\app\Http\Controllers\Auth\RegisterController as BackpackRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends BackpackRegisterController
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $this->data['title'] = trans('backpack::base.register');

        return view(backpack_view('auth.register'), $this->data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|philippine_phone',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'terms' => 'required|accepted',
            // 'privacy' => 'required|accepted',
        ]);

        // Create the user with default patient role and user_type
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'patient',
            'status' => 'active',
            'registration_status' => 'Full'
        ]);

        // Create patient profile with basic information
        $user->patientProfile()->create([
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'civil_status' => null, // Will be filled later
            'emergency_contact_name' => null, // Will be filled later
            'emergency_contact_phone' => null, // Will be filled later
            'emergency_contact_relationship' => null, // Will be filled later
        ]);

        // Assign the Patient role
        $user->assignRole('Patient');

        // Redirect to login page
        return redirect()->route('backpack.auth.login')->with('success', 'Registration successful! Please log in');
    }
}
