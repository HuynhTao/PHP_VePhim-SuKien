<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GheNgoi extends Model
{
    protected $table = 'ghe_ngoi';

    protected $fillable = ['phong_chieu_id', 'hang_ghe', 'so_ghe', 'loai_ghe', 'dang_su_dung'];

    protected function casts(): array
    {
        return ['dang_su_dung' => 'boolean'];
    }

    public function phongChieu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PhongChieu::class, 'phong_chieu_id');
    }

    public function gheTheoSuats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GheTheoSuat::class, 'ghe_ngoi_id');
    }
}

