<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phim extends Model
{
    protected $table = 'phim';
    protected $primaryKey = 'noi_dung_id';
    public $incrementing = false;

    protected $fillable = ['noi_dung_id', 'thoi_luong_phut', 'gioi_han_tuoi', 'ngon_ngu', 'ngay_khoi_chieu'];

    protected function casts(): array
    {
        return ['ngay_khoi_chieu' => 'date'];
    }

    public function noiDung(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(NoiDung::class, 'noi_dung_id');
    }
}

