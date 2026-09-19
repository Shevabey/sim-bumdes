<?php

namespace App\Console\Commands;

use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Console\Command;

class ReferralVerifyCheck extends Command
{
    /**
     * Signature artisan: referral:verify-check
     * Dijadwalkan harian (daily) di routes/console.php.
     */
    protected $signature = 'referral:verify-check';

    protected $description = 'Cek referral pending, cairkan jika syarat aktivitas terpenuhi dalam 15 hari, atau tandai gagal';

    public function handle(ReferralService $service): int
    {
        $pendingList = Referral::where('status', 'pending')->get();

        foreach ($pendingList as $referral) {
            if ($service->cekAktivitasMinimal($referral)) {
                // Syarat aktivitas terpenuhi — cairkan bonus ke kas pengaju
                $service->cairkan($referral);
            } elseif (now()->greaterThan($referral->batas_verifikasi)) {
                // Batas verifikasi 15 hari sudah habis, tandai gagal
                $referral->update(['status' => 'gagal']);
            }
        }

        $this->info("{$pendingList->count()} referral pending diproses.");

        return self::SUCCESS;
    }
}
