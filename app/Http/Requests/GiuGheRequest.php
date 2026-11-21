<?php

namespace App\Http\Requests;

use App\Services\DatVeService;
use Illuminate\Foundation\Http\FormRequest;

class GiuGheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // đã có middleware auth + role
    }

    public function rules(): array
    {
        return [
            'ghe_ids' => ['required', 'array', 'min:1', 'max:'.DatVeService::TOI_DA_GHE],
            'ghe_ids.*' => ['integer', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'ghe_ids.required' => 'Vui lòng chọn ít nhất 1 ghế.',
            'ghe_ids.min' => 'Vui lòng chọn ít nhất 1 ghế.',
            'ghe_ids.max' => 'Mỗi đơn chỉ được đặt tối đa '.DatVeService::TOI_DA_GHE.' ghế.',
            'ghe_ids.array' => 'Dữ liệu ghế không hợp lệ.',
            'ghe_ids.*.integer' => 'Dữ liệu ghế không hợp lệ.',
            'ghe_ids.*.distinct' => 'Không được chọn trùng ghế.',
        ];
    }
}