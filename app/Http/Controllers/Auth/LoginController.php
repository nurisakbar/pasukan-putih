<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Redirect setelah login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login untuk web dan API.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek apakah request dari API atau web
            if ($request->expectsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['message' => 'Login berhasil', 'token' => $token, 'user' => $user]);
            }

            return redirect()->intended($this->redirectTo);
        }

        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    /**
     * Login berdasarkan email tanpa password (untuk API saja).
     */
    public function loginByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email tidak ditemukan'], 404);
            }
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        Auth::login($user);
        
        // Check if the request expects JSON (API call)
        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'Login berhasil', 'token' => $token, 'user' => $user]);
        }
        
        // For web requests, redirect to home
        return redirect()->intended($this->redirectTo);
    }

    /**
     * Logout untuk web dan API.
     */
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
                return response()->json(['message' => 'Logout berhasil']);
            }
            return response()->json(['message' => 'Tidak ada sesi login'], 401);
        }

        Auth::logout();
        Session::flush();
        return redirect('/login');
    }
}