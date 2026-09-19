<?php

namespace App\Console\Commands;

use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Console\Command;

class ReferralExpireCheck extends Command
{
    /**
     * Signature artisan: referral:expire-check
     * Dijadwalkan setiap jam (hourly) di routes/console.php.
     */
    protected $signature = 'referral:expire-check';

    protected $description = 'Cek dan tandai kode referral yang sudah lewat 5 hari sebagai kedaluwarsa';

    public function handle(ReferralService $service): int
    {
        $expired = Referral::where('status', 'aktif')
            ->where('tanggal_expired', '<', now())
            ->get();

        foreach ($expired as $referral) {
            $referral->update(['status' => 'kedaluwarsa']);

            // Generate kode baru otomatis untuk BUMDes pengaju
            $service->generateKode($referral->bumdesPengaju);
        }

        $this->info("{$expired->count()} kode referral kedaluwarsa diproses.");

        return self::SUCCESS;
    }
}
