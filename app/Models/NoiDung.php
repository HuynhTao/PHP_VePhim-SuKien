<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class NoiDung extends Model
{
    protected $table = 'noi_dung';

    protected $fillable = ['loai_noi_dung', 'tieu_de', 'mo_ta', 'anh_bia', 'trang_thai'];

    protected function casts(): array
    {
        return [];
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

    /** M2: chỉ lấy nội dung đã công bố. */
    public function scopeCongBo(Builder $query): Builder
    {
        return $query->where('trang_thai', 'cong_bo');
    }

    /** M2: URL poster, dùng $item->anh_bia_url trong Blade (null nếu chưa có ảnh). */
    protected function anhBiaUrl(): Attribute
    {
        return Attribute::get(fn () => $this->anh_bia ? asset('storage/'.$this->anh_bia) : null);
    }
}
