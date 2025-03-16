<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $ip = $request->ip();
        $attempts = Session::get('login_attempts_' . $ip, 0);
        $lastAttemptTime = Session::get('login_attempts_' . $ip . '_time', 0);
        $maxAttempts = 5;
        $lockoutTime = 600; // 10 menit dalam detik

        if ($attempts >= $maxAttempts && (time() - $lastAttemptTime) < $lockoutTime) {
            $remainingTime = $lockoutTime - (time() - $lastAttemptTime);
            $formattedTime = $this->formatTime($remainingTime);

            return redirect()->back()->with([
                'lockoutTime' => $remainingTime
            ]);
        }

        if (Auth::attempt($credentials)) {
            // Reset attempts on successful login
            Session::forget('login_attempts_' . $ip);
            Session::forget('login_attempts_' . $ip . '_time');
            return redirect()->intended('home');
        }

        // Increment attempts
        $attempts++;
        $lastAttemptTime = time();
        Session::put('login_attempts_' . $ip, $attempts);
        Session::put('login_attempts_' . $ip . '_time', $lastAttemptTime);

        return redirect()->back()->with([
            'message' => 'Email atau password salah',
            'lockoutTime' => 0
        ]);
    }

    protected function formatTime($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return $minutes . ' menit ' . $seconds . ' detik';
    }

       /**
     * Login user by email only (no password required)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function loginByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended('home');
        }

        return redirect()->back()->with('message', 'Email tidak terdaftar');
    }

    /**
     * Show login by email form
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginByEmailForm()
    {
        return view('auth.email.form');
    }
}
