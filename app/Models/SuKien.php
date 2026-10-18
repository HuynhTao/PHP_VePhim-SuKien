<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuKien extends Model
{
    protected $table = 'su_kien';
    protected $primaryKey = 'noi_dung_id';
    public $incrementing = false;

    protected $fillable = ['noi_dung_id', 'don_vi_to_chuc', 'the_loai', 'gioi_han_tuoi'];

    protected function casts(): array
    {
        return [];
    }

    public function noiDung(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(NoiDung::class, 'noi_dung_id');
    }
}

