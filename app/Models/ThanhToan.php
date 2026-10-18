<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanh_toan';

    protected $fillable = ['don_hang_id', 'nha_cung_cap', 'ma_giao_dich', 'so_tien', 'trang_thai', 'thanh_toan_luc', 'du_lieu_phan_hoi'];

    protected function casts(): array
    {
        return ['so_tien' => 'decimal:2', 'thanh_toan_luc' => 'datetime', 'du_lieu_phan_hoi' => 'array'];
    }

    public function donHang(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }
}

