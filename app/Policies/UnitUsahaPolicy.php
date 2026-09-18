<?php

namespace App\Policies;

use App\Models\Akun;
use App\Models\UnitUsaha;

class UnitUsahaPolicy
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

    public function view(Akun $akun, UnitUsaha $unit): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasAnyRole($akun, ['admin_bumdes', 'sekretaris', 'bendahara'])) {
            return $this->sameBumdes($akun, $unit);
        }

        return $this->hasRole($akun, 'admin_unit') && $this->sameUnit($akun, $unit);
    }

    public function create(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes']);
    }

    public function update(Akun $akun, UnitUsaha $unit): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        if ($this->hasRole($akun, 'admin_bumdes') && $this->sameBumdes($akun, $unit)) {
            return true;
        }

        return false;
    }

    public function toggleStatus(Akun $akun, UnitUsaha $unit): bool
    {
        return $this->hasRole($akun, 'admin_bumdes') && $this->sameBumdes($akun, $unit);
    }

    public function delete(Akun $akun, UnitUsaha $unit): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(Akun $akun, UnitUsaha $unit): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(Akun $akun, UnitUsaha $unit): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    private function sameBumdes(Akun $akun, UnitUsaha $unit): bool
    {
        return $akun->id_bumdes !== null && $akun->id_bumdes === $unit->id_bumdes;
    }

    private function sameUnit(Akun $akun, UnitUsaha $unit): bool
    {
        return $akun->id_unit !== null && $akun->id_unit === $unit->id_unit;
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
