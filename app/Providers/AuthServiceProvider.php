<?php

namespace App\Providers;

use App\Models\Bumdes;
use App\Models\Referral;
use App\Models\Tagihan;
use App\Models\Transaksi;
use App\Models\UnitUsaha;
use App\Policies\BumdesPolicy;
use App\Policies\ReferralPolicy;
use App\Policies\TagihanPolicy;
use App\Policies\TransaksiPolicy;
use App\Policies\UnitUsahaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Bumdes::class => BumdesPolicy::class,
        UnitUsaha::class => UnitUsahaPolicy::class,
        Transaksi::class => TransaksiPolicy::class,
        Tagihan::class => TagihanPolicy::class,
        Referral::class => ReferralPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
