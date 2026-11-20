<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NoiDungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /** Khi sửa, loại phim/sự kiện lấy từ bản ghi, không cho đổi. */
    protected function prepareForValidation(): void
    {
        if ($noiDung = $this->route('noiDung')) {
            $this->merge(['loai_noi_dung' => $noiDung->loai_noi_dung]);
        }
    }

    public function rules(): array
    {
        $laPhim = $this->input('loai_noi_dung') === 'phim';

        return [
            'loai_noi_dung' => ['required', 'in:phim,su_kien'],
            'tieu_de' => ['required', 'string', 'max:255'],
            'mo_ta' => ['nullable', 'string', 'max:5000'],
            'trang_thai' => ['required', 'in:nhap,cong_bo,an'],
            'anh_bia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gioi_han_tuoi' => ['required', 'in:P,T13,T16,T18'],
            // Chỉ bắt buộc với phim
            'thoi_luong_phut' => [Rule::requiredIf($laPhim), 'nullable', 'integer', 'min:1', 'max:600'],
            'ngon_ngu' => [Rule::requiredIf($laPhim), 'nullable', 'string', 'max:100'],
            'ngay_khoi_chieu' => [Rule::requiredIf($laPhim), 'nullable', 'date'],
            // Chỉ bắt buộc với sự kiện
            'don_vi_to_chuc' => [Rule::requiredIf(! $laPhim), 'nullable', 'string', 'max:255'],
            'the_loai' => [Rule::requiredIf(! $laPhim), 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'date' => ':attribute không đúng định dạng ngày.',
            'anh_bia.image' => 'Poster phải là file ảnh.',
            'anh_bia.mimes' => 'Poster chỉ nhận định dạng JPG, PNG hoặc WEBP.',
            'anh_bia.max' => 'Poster không được vượt quá 2 MB.',
            'anh_bia.uploaded' => 'Tải poster thất bại (file quá lớn hoặc bị lỗi).',
            'thoi_luong_phut.min' => 'Thời lượng phải ít nhất :min phút.',
            'thoi_luong_phut.max' => 'Thời lượng không được vượt quá :max phút.',
        ];
    }

    public function attributes(): array
    {
        return [
            'loai_noi_dung' => 'Loại nội dung',
            'tieu_de' => 'Tiêu đề',
            'mo_ta' => 'Mô tả',
            'trang_thai' => 'Trạng thái',
            'anh_bia' => 'Poster',
            'gioi_han_tuoi' => 'Giới hạn tuổi',
            'thoi_luong_phut' => 'Thời lượng',
            'ngon_ngu' => 'Ngôn ngữ',
            'ngay_khoi_chieu' => 'Ngày khởi chiếu',
            'don_vi_to_chuc' => 'Đơn vị tổ chức',
            'the_loai' => 'Thể loại',
        ];
    }
}
