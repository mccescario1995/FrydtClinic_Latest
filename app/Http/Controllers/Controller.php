<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('homepage');
    }
}
