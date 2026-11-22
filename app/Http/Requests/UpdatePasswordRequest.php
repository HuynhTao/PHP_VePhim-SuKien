<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! Hash::check((string) $value, (string) $this->user()?->mat_khau)) {
                        $fail('Mật khẩu hiện tại không chính xác.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
