<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suat extends Model
{
    protected $table = 'suat';

    protected $fillable = ['noi_dung_id', 'phong_chieu_id', 'bat_dau_luc', 'ket_thuc_luc', 'trang_thai'];

    protected function casts(): array
    {
        return ['bat_dau_luc' => 'datetime', 'ket_thuc_luc' => 'datetime'];
    }

    public function noiDung(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(NoiDung::class, 'noi_dung_id');
    }

    public function phongChieu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PhongChieu::class, 'phong_chieu_id');
    }

    public function gheTheoSuats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GheTheoSuat::class, 'suat_id');
    }
}

