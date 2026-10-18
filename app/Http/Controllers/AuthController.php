<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'ho_ten' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:nguoi_dung,email'],
            'so_dien_thoai' => ['nullable', 'regex:/^[0-9+() .-]{8,20}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create([
            'ho_ten' => $data['ho_ten'],
            'email' => $data['email'],
            'so_dien_thoai' => $data['so_dien_thoai'] ?? null,
            'mat_khau' => $data['password'],
            'vai_tro' => 'user',
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('home')->with('success', 'Đăng ký thành công. Chào mừng bạn!');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended(route(Auth::user()->isAdmin() ? 'admin.users.index' : 'home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Bạn đã đăng xuất.');
    }
}

