<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GheTheoSuat extends Model
{
    protected $table = 'ghe_theo_suat';

    protected $fillable = ['suat_id', 'ghe_ngoi_id', 'gia_ve', 'trang_thai', 'giu_den_luc'];

    protected function casts(): array
    {
        return ['gia_ve' => 'decimal:2', 'giu_den_luc' => 'datetime'];
    }

    public function suat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Suat::class, 'suat_id');
    }

    public function gheNgoi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GheNgoi::class, 'ghe_ngoi_id');
    }

    public function ve(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ve::class, 'ghe_theo_suat_id');
    }
}

