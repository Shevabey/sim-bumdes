<?php

namespace App\Console\Commands;

use App\Services\IuranService;
use Illuminate\Console\Command;

class GenerateIuranBulanan extends Command
{
    /**
     * Signature artisan: iuran:generate-bulanan
     * Dijadwalkan tiap tanggal 1 pukul 00:05 di routes/console.php.
     */
    protected $signature = 'iuran:generate-bulanan';

    protected $description = 'Generate tagihan iuran bulanan Rp50.000 untuk seluruh BUMDes aktif';

    public function handle(IuranService $service): int
    {
        $jumlah = $service->generateBulanan();

        $this->info("Berhasil membuat {$jumlah} tagihan iuran baru.");

        return self::SUCCESS;
    }
}
