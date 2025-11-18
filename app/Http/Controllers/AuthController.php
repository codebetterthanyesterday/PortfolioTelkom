<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showAdminLoginForm()
    {
        return view('admin.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Only allow admin role
            if (!$user->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Access denied. Admin credentials required.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isStudent() || $user->isInvestor()) {
                return redirect()->intended(route('home'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'role' => 'required|in:student,investor',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        // Create user with minimal information
        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Create empty role-specific record
        if ($validated['role'] === 'student') {
            Student::create([
                'user_id' => $user->id,
                'student_id' => null, // Will be filled in profile
            ]);
        } elseif ($validated['role'] === 'investor') {
            Investor::create([
                'user_id' => $user->id,
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to profile completion or dashboard
        return redirect()->route('home')
            ->with('success', 'Registrasi berhasil! Silakan lengkapi profil Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Forgot Password - Show Form
    public function showForgotPassword()
    {
        return view('forgot');
    }

    // Forgot Password - Send Reset Link
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Reset Password - Show Form
    public function showResetForm(Request $request, $token)
    {
        return view('recovery', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Reset Password - Process Reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
