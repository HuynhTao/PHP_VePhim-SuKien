<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoiDung extends Model
{
    protected $table = 'noi_dung';

    protected $fillable = ['loai_noi_dung', 'tieu_de', 'mo_ta', 'anh_bia', 'trang_thai'];

    protected function casts(): array
    {
        return [];
    }

        /** URL poster: ảnh trong public/, trong storage, hoặc link http; không có thì null. */
    public function anhBiaUrl(): ?string
    {
        $p = $this->anh_bia;
        if (! $p) {
            return null;
        }
        if (str_starts_with($p, 'http')) {
            return $p;
        }
        if (file_exists(public_path($p))) {
            return asset($p);
        }
        if (file_exists(public_path('storage/'.$p))) {
            return asset('storage/'.$p);
        }

        return null;
    }

    public function phim(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Phim::class, 'noi_dung_id');
    }

    public function suKien(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SuKien::class, 'noi_dung_id');
    }

    public function suats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Suat::class, 'noi_dung_id');
    }
}

