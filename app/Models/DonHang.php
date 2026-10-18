<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'don_hang';

    protected $fillable = ['nguoi_dung_id', 'ma_don_hang', 'trang_thai', 'tam_tinh', 'tien_giam', 'tong_tien', 'het_han_luc', 'dat_luc'];

    protected function casts(): array
    {
        return ['tam_tinh' => 'decimal:2', 'tien_giam' => 'decimal:2', 'tong_tien' => 'decimal:2', 'dat_luc' => 'datetime', 'het_han_luc' => 'datetime'];
    }

    public function nguoiDung(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function ves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ve::class, 'don_hang_id');
    }

    public function thanhToans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ThanhToan::class, 'don_hang_id');
    }
}

