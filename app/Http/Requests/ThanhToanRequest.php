<?php

namespace App\Http\Requests;

use App\Services\DatVeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ThanhToanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['phuong_thuc' => ['required', Rule::in(array_keys(DatVeService::PHUONG_THUC))]];
    }

    public function messages(): array
    {
        return [
            'phuong_thuc.required' => 'Vui lòng chọn phương thức thanh toán.',
            'phuong_thuc.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}