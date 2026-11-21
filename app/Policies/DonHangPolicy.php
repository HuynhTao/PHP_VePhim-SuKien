<?php

namespace App\Policies;

use App\Models\DonHang;
use App\Models\User;

class DonHangPolicy
{
    public function view(User $user, DonHang $donHang): bool
    {
        return $user->isAdmin() || $user->id === $donHang->nguoi_dung_id;
    }

    public function pay(User $user, DonHang $donHang): bool
    {
        return $user->id === $donHang->nguoi_dung_id;
    }
}