<?php

namespace App\Policies;

use App\Models\Tagihan;

class TagihanPolicy
{
    public function viewAny(object $akun): bool
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

    public function view(object $akun, Tagihan $tagihan): bool
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

    public function create(object $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes', 'bendahara', 'admin_unit']);
    }

    public function update(object $akun, Tagihan $tagihan): bool
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

    public function verify(object $akun, Tagihan $tagihan): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        return $this->hasAnyRole($akun, ['admin_bumdes', 'bendahara'])
            && $akun->id_bumdes !== null
            && $tagihan->unitUsaha?->id_bumdes === $akun->id_bumdes;
    }

    public function payCash(object $akun, Tagihan $tagihan): bool
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

    public function delete(object $akun, Tagihan $tagihan): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(object $akun, Tagihan $tagihan): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(object $akun, Tagihan $tagihan): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    private function hasRole(object $akun, string $role): bool
    {
        if (method_exists($akun, 'hasRole')) {
            return $akun->hasRole($role);
        }

        return $akun->role === $role;
    }

    private function hasAnyRole(object $akun, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($akun, $role)) {
                return true;
            }
        }

        return false;
    }
}
