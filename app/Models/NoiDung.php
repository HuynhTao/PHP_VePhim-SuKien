<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NoiDung extends Model
{
    protected $table = 'noi_dung';

    protected $fillable = ['loai_noi_dung', 'tieu_de', 'mo_ta', 'anh_bia', 'trang_thai'];

    protected function casts(): array
    {
        return [];
    }

    /** M2: scope chỉ lấy nội dung đã công bố. */
    public function scopeCongBo(Builder $query): Builder
    {
        return $query->where('trang_thai', 'cong_bo');
    }

    /** URL poster: hỗ trợ cả gọi hàm $content->anhBiaUrl() (M1/M4) và thuộc tính $content->anh_bia_url (M2). */
    public function anhBiaUrl(): ?string
    {
        $p = $this->anh_bia;
        if ($p) {
            if (str_starts_with($p, 'http')) {
                return $p;
            }
            if (file_exists(public_path($p))) {
                return asset($p);
            }
            if (file_exists(public_path('storage/'.$p))) {
                return asset('storage/'.$p);
            }
        }

        foreach (["images/{$this->id}.png", "images/posters/{$this->id}.png"] as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return null;
    }

    public function getAnhBiaUrlAttribute(): ?string
    {
        return $this->anhBiaUrl();
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
