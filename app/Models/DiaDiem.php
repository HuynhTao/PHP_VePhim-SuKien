<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaDiem extends Model
{
    protected $table = 'dia_diem';

    protected $fillable = ['ten_dia_diem', 'dia_chi', 'tinh_thanh', 'so_dien_thoai', 'trang_thai'];

    protected function casts(): array
    {
        return [];
    }

    public function phongChieus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PhongChieu::class, 'dia_diem_id');
    }
}

