<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongChieu extends Model
{
    protected $table = 'phong_chieu';

    protected $fillable = ['dia_diem_id', 'ten_phong', 'loai_phong', 'trang_thai'];

    protected function casts(): array
    {
        return [];
    }

    public function diaDiem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiaDiem::class, 'dia_diem_id');
    }

    public function gheNgois(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GheNgoi::class, 'phong_chieu_id');
    }

    public function suats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Suat::class, 'phong_chieu_id');
    }
}

