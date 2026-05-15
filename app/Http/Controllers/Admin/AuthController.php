<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'البريد الإلكتروني مطلوب.',
            'email.email'       => 'البريد الإلكتروني غير صالح.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min'      => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // تحديث آخر تسجيل دخول
            Auth::guard('admin')->user()->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'مرحباً بعودتك، ' . Auth::guard('admin')->user()->name . '!');
        }

        return back()->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'])
                     ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}
