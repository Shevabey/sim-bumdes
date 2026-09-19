<?php

namespace App\Services;

use App\Models\Bumdes;
use App\Models\IuranBumdes;
use App\Models\KasBumdes;
use App\Models\KasMutasi;

class IuranService
{
    /**
     * Generate tagihan iuran bulanan Rp50.000 untuk seluruh BUMDes aktif.
     * Hanya dibuat jika belum ada record iuran untuk bulan yang sama.
     *
     * @return int Jumlah tagihan iuran baru yang berhasil dibuat
     */
    public function generateBulanan(): int
    {
        $bulanIni     = now()->format('Y-m');
        $jumlahDibuat = 0;

        Bumdes::where('status_aktif', true)->each(function (Bumdes $bumdes) use ($bulanIni, &$jumlahDibuat) {
            $sudahAda = IuranBumdes::where('id_bumdes', $bumdes->id_bumdes)
                ->where('bulan_tahun', $bulanIni)
                ->exists();

            if (! $sudahAda) {
                IuranBumdes::create([
                    'id_iuran'    => "IUR-{$bumdes->id_bumdes}-{$bulanIni}",
                    'id_bumdes'   => $bumdes->id_bumdes,
                    'bulan_tahun' => $bulanIni,
                    'jumlah'      => 50000,
                    'status'      => 'belum_bayar',
                ]);
                $jumlahDibuat++;
            }
        });

        return $jumlahDibuat;
    }

    /**
     * Bayar iuran menggunakan saldo kas BUMDes.
     * Abort jika saldo kas tidak mencukupi.
     */
    public function bayarDariKas(IuranBumdes $iuran): void
    {
        $kas = KasBumdes::where('id_bumdes', $iuran->id_bumdes)->firstOrFail();

        abort_if($kas->saldo < $iuran->jumlah, 422, 'Saldo kas tidak mencukupi.');

        $kas->decrement('saldo', $iuran->jumlah);

        KasMutasi::create([
            'id_kas'     => $kas->id_kas,
            'tipe'       => 'keluar',
            'jumlah'     => $iuran->jumlah,
            'sumber'     => 'iuran',
            'keterangan' => "Bayar iuran {$iuran->bulan_tahun} dari kas",
            'tanggal'    => now(),
        ]);

        $iuran->update([
            'status'      => 'lunas',
            'sumber_dana' => 'kas',
            'tanggal_bayar' => now(),
        ]);
    }
}
