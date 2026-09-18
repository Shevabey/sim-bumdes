<?php

namespace App\Policies;

use App\Models\Akun;
use App\Models\Bumdes;

class BumdesPolicy
{
    public function viewAny(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, [
            'super_admin',
            'pengawas',
            'penasihat',
            'direktur',
            'admin_bumdes',
            'sekretaris',
            'bendahara',
        ]);
    }

    public function view(Akun $akun, Bumdes $bumdes): bool
    {
        if ($this->hasAnyRole($akun, ['super_admin', 'pengawas', 'penasihat', 'direktur'])) {
            return true;
        }

        return $this->sameBumdes($akun, $bumdes);
    }

    public function create(Akun $akun): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function update(Akun $akun, Bumdes $bumdes): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        return $this->hasRole($akun, 'admin_bumdes') && $this->sameBumdes($akun, $bumdes);
    }

    public function toggleStatus(Akun $akun, Bumdes $bumdes): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function delete(Akun $akun, Bumdes $bumdes): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(Akun $akun, Bumdes $bumdes): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(Akun $akun, Bumdes $bumdes): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    private function sameBumdes(Akun $akun, Bumdes $bumdes): bool
    {
        return $akun->id_bumdes !== null && $akun->id_bumdes === $bumdes->id_bumdes;
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
