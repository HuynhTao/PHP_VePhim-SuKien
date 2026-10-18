<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ve extends Model
{
    protected $table = 've';

    protected $fillable = ['don_hang_id', 'ghe_theo_suat_id', 'ma_ve', 'trang_thai', 'gia_ban', 'phat_hanh_luc'];

    protected function casts(): array
    {
        return ['gia_ban' => 'decimal:2', 'phat_hanh_luc' => 'datetime'];
    }

    public function donHang(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }

    public function gheTheoSuat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GheTheoSuat::class, 'ghe_theo_suat_id');
    }
}

