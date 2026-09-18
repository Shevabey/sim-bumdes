<?php

namespace App\Policies;

use App\Models\Akun;
use App\Models\Tagihan;

class TagihanPolicy
{
    public function viewAny(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, [
            'super_admin',
            'admin_bumdes',
            'sekretaris',
            'bendahara',
            'admin_unit',
            'pengguna',
        ]);
    }

    public function view(Akun $akun, Tagihan $tagihan): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'sekretaris', 'bendahara'])) {
            return $akun->id_bumdes !== null
                && $tagihan->unitUsaha?->id_bumdes === $akun->id_bumdes;
        }

        if ($this->hasRole($akun, 'admin_unit')) {
            return $akun->id_unit !== null && $akun->id_unit === $tagihan->id_unit;
        }

        return $this->hasRole($akun, 'pengguna')
            && $tagihan->pelanggan?->id_akun === $akun->id_akun;
    }

    public function create(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes', 'bendahara', 'admin_unit']);
    }

    public function update(Akun $akun, Tagihan $tagihan): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'bendahara'])) {
            return $akun->id_bumdes !== null
                && $tagihan->unitUsaha?->id_bumdes === $akun->id_bumdes;
        }

        return $this->hasRole($akun, 'admin_unit')
            && $akun->id_unit !== null
            && $akun->id_unit === $tagihan->id_unit;
    }

    public function verify(Akun $akun, Tagihan $tagihan): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        return $this->hasAnyRole($akun, ['admin_bumdes', 'bendahara'])
            && $akun->id_bumdes !== null
            && $tagihan->unitUsaha?->id_bumdes === $akun->id_bumdes;
    }

    public function payCash(Akun $akun, Tagihan $tagihan): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'bendahara'])) {
            return $akun->id_bumdes !== null
                && $tagihan->unitUsaha?->id_bumdes === $akun->id_bumdes;
        }

        return $this->hasRole($akun, 'admin_unit')
            && $akun->id_unit !== null
            && $akun->id_unit === $tagihan->id_unit;
    }

    public function delete(Akun $akun, Tagihan $tagihan): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(Akun $akun, Tagihan $tagihan): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(Akun $akun, Tagihan $tagihan): bool
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
