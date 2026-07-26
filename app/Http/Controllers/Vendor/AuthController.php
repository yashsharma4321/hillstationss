<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role == 'vendor') {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendor.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role !== 'vendor') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'The provided credentials are not for a vendor account.',
                ]);
            }

            // Check if vendor is approved
            if (!$user->vendor || !$user->vendor->is_approved) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your vendor account is pending admin approval. Please try again once approved.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('vendor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('vendor.login');
    }
}
