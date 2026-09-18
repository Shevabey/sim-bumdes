<?php

namespace App\Policies;

use App\Models\Akun;
use App\Models\Transaksi;

class TransaksiPolicy
{
    public function viewAny(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, [
            'super_admin',
            'admin_bumdes',
            'sekretaris',
            'bendahara',
            'admin_unit',
        ]);
    }

    public function view(Akun $akun, Transaksi $transaksi): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'sekretaris', 'bendahara'])) {
            return $akun->id_bumdes !== null
                && $transaksi->unitUsaha?->id_bumdes === $akun->id_bumdes;
        }

        return $this->hasRole($akun, 'admin_unit')
            && $akun->id_unit !== null
            && $akun->id_unit === $transaksi->id_unit;
    }

    public function create(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes', 'bendahara', 'admin_unit']);
    }

    public function update(Akun $akun, Transaksi $transaksi): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'bendahara'])) {
            return $akun->id_bumdes !== null
                && $transaksi->unitUsaha?->id_bumdes === $akun->id_bumdes;
        }

        return $this->hasRole($akun, 'admin_unit')
            && $akun->id_unit !== null
            && $akun->id_unit === $transaksi->id_unit;
    }

    public function delete(Akun $akun, Transaksi $transaksi): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(Akun $akun, Transaksi $transaksi): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(Akun $akun, Transaksi $transaksi): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    private function hasRole(Akun $akun, string $role): bool
    {
        if (method_exists($akun, 'hasRole')) {
            return $akun->hasRole($role);
        }

        return $akun->role === $role;
    }

    private function hasAnyRole(Akun $akun, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($akun, $role)) {
                return true;
            }
        }

        return false;
    }
}
