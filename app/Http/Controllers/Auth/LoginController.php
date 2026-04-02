<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Auth\LoginController as BackpackLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BackpackLoginController
{
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Reset OTP verification on every login to require OTP every time


        // Check if user has Patient role
        // if ($user->hasRole('Patient')) {
        //     return redirect()->route('patient.dashboard');
        // }

        if ($user && $user->hasRole('Patient')) {
            // redirect patients to patient dashboard
            return redirect(route('patient.dashboard'));
        }

        // For admin/staff users, redirect to admin dashboard
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Get the post-register / post-login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('backpack.base.route_prefix', 'admin') . '/dashboard';
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect appropriately
        if (backpack_auth()->check()) {
            if (backpack_user()->hasRole('Patient')) {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $this->data['title']    = trans('backpack::base.login'); // set the page title
        $this->data['username'] = $this->username();

        return view(backpack_view('auth.login'), $this->data);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        // DO THIS IN VENDER backpack/crud/src/app/library/auth/authenticatesusers.php

        $user = backpack_auth()->user();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        User::where('id', $user->id)->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_verified_at' => null,
            'otp_attempts' => 0,
            'otp_last_sent_at' => null
        ]);

        return redirect()->route('homepage');
    }
}
