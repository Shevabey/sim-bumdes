<?php

namespace App\Policies;

use App\Models\Akun;
use App\Models\Referral;

class ReferralPolicy
{
    public function viewAny(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes']);
    }

    public function view(Akun $akun, Referral $referral): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        return $this->hasRole($akun, 'admin_bumdes')
            && $akun->id_bumdes !== null
            && (
                $akun->id_bumdes === $referral->id_bumdes_pengaju
                || $akun->id_bumdes === $referral->id_bumdes_penerima
            );
    }

    public function create(Akun $akun): bool
    {
        return $this->generate($akun);
    }

    public function generate(Akun $akun): bool
    {
        return $this->hasAnyRole($akun, ['super_admin', 'admin_bumdes']);
    }

    public function redeem(Akun $akun, Referral $referral): bool
    {
        return $this->hasRole($akun, 'admin_bumdes')
            && $akun->id_bumdes !== null
            && $akun->id_bumdes !== $referral->id_bumdes_pengaju
            && $referral->id_bumdes_penerima === null;
    }

    public function update(Akun $akun, Referral $referral): bool
    {
        if ($this->hasRole($akun, 'super_admin')) {
            return true;
        }

        return $this->hasRole($akun, 'admin_bumdes')
            && $akun->id_bumdes !== null
            && $akun->id_bumdes === $referral->id_bumdes_pengaju;
    }

    public function delete(Akun $akun, Referral $referral): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function restore(Akun $akun, Referral $referral): bool
    {
        return $this->hasRole($akun, 'super_admin');
    }

    public function forceDelete(Akun $akun, Referral $referral): bool
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
