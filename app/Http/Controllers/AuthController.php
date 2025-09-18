<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $loginField = $request->input('email');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Try to login with email or username
        $credentials = filter_var($loginField, FILTER_VALIDATE_EMAIL) 
            ? ['email' => $loginField, 'password' => $password]
            : ['username' => $loginField, 'password' => $password];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user has sales or owner role
            if (!$user->canAccessApp()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Anda tidak memiliki akses ke aplikasi ini. Hanya Sales dan Owner yang dapat mengakses.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email/Username atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}