<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'vai_tro' => ['nullable', 'in:admin,user']]);
        $users = User::query()
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query
                ->where('ho_ten', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%')))
            ->when($filters['vai_tro'] ?? null, fn ($query, $role) => $query->where('vai_tro', $role))
            ->latest('id')->paginate(10)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['vai_tro' => 'user'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['mat_khau'] = $data['password'];
        unset($data['password']);
        $user = User::create($data);
        return redirect()->route('admin.users.show', $user)->with('success', 'Đã tạo tài khoản.');
    }

    public function show(User $user)
    {
        $user->loadCount('donHangs');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if ($user->is($request->user()) && $data['vai_tro'] !== 'admin') {
            throw ValidationException::withMessages(['vai_tro' => 'Bạn không thể tự gỡ quyền Admin của mình.']);
        }
        if (! empty($data['password'])) {
            $data['mat_khau'] = $data['password'];
        }
        unset($data['password']);
        $user->update($data);
        return redirect()->route('admin.users.show', $user)->with('success', 'Đã cập nhật tài khoản.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            throw ValidationException::withMessages(['user' => 'Bạn không thể xóa tài khoản đang đăng nhập.']);
        }
        DB::transaction(function () use ($user) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($locked->donHangs()->exists()) {
                throw ValidationException::withMessages(['user' => 'Tài khoản đã có đơn hàng, không thể xóa để bảo toàn lịch sử giao dịch.']);
            }
            DB::table('sessions')->where('user_id', $locked->id)->delete();
            $locked->delete();
        });
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        return $request->validate([
            'ho_ten' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('nguoi_dung', 'email')->ignore($user?->id)],
            'so_dien_thoai' => ['nullable', 'regex:/^[0-9+() .-]{8,20}$/'],
            'vai_tro' => ['required', Rule::in(['admin', 'user'])],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ]);
    }
}

