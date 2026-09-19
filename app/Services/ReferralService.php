<?php

namespace App\Services;

use App\Models\Bumdes;
use App\Models\KasBumdes;
use App\Models\KasMutasi;
use App\Models\Referral;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Generate kode referral baru untuk BUMDes pengaju.
     * Kode lama yang masih aktif akan dinonaktifkan (status: kedaluwarsa).
     */
    public function generateKode(Bumdes $pengaju): Referral
    {
        // Nonaktifkan kode aktif lama milik BUMDes ini (jika ada)
        Referral::where('id_bumdes_pengaju', $pengaju->id_bumdes)
            ->where('status', 'aktif')
            ->update(['status' => 'kedaluwarsa']);

        $kode = strtoupper(Str::random(6));

        return Referral::create([
            'id_referral'       => "REF-{$pengaju->id_bumdes}-{$kode}",
            'id_bumdes_pengaju' => $pengaju->id_bumdes,
            'kode_unik'         => $kode,
            'tanggal_generate'  => now(),
            'tanggal_expired'   => now()->addDays(5),
            'status'            => 'aktif',
        ]);
    }

    /**
     * Redeem kode referral oleh BUMDes penerima.
     * Status berubah menjadi 'pending', batas verifikasi 15 hari.
     * Otomatis generate kode baru untuk BUMDes pengaju.
     */
    public function redeem(string $kodeUnik, Bumdes $penerima): Referral
    {
        $referral = Referral::where('kode_unik', $kodeUnik)
            ->where('status', 'aktif')
            ->where('tanggal_expired', '>=', now())
            ->firstOrFail();

        // Cegah pasangan pengaju-penerima yang sama redeem berulang kali
        $sudahPernah = Referral::where('id_bumdes_pengaju', $referral->id_bumdes_pengaju)
            ->where('id_bumdes_penerima', $penerima->id_bumdes)
            ->whereIn('status', ['pending', 'cair'])
            ->exists();

        abort_if($sudahPernah, 422, 'BUMDes ini sudah pernah melakukan redeem dari pengaju yang sama.');

        $referral->update([
            'id_bumdes_penerima' => $penerima->id_bumdes,
            'status'             => 'pending',
            'tanggal_redeem'     => now(),
            'batas_verifikasi'   => now()->addDays(15),
        ]);

        // Otomatis generate kode baru untuk BUMDes pengaju
        $this->generateKode($referral->bumdesPengaju);

        return $referral;
    }

    /**
     * Cek apakah BUMDes penerima sudah memenuhi syarat aktivitas minimal:
     * - Ada transaksi di salah satu unit usaha setelah tanggal redeem, ATAU
     * - Ada iuran yang lunas setelah tanggal redeem.
     */
    public function cekAktivitasMinimal(Referral $referral): bool
    {
        $penerima = $referral->bumdesPenerima;

        $adaTransaksi = $penerima->units()
            ->whereHas('transaksi', fn ($q) => $q->where('tanggal', '>=', $referral->tanggal_redeem))
            ->exists();

        $adaIuranLunas = $penerima->iuran()
            ->where('status', 'lunas')
            ->where('tanggal_bayar', '>=', $referral->tanggal_redeem)
            ->exists();

        return $adaTransaksi || $adaIuranLunas;
    }

    /**
     * Cairkan referral: tambah saldo kas BUMDes pengaju Rp10.000,
     * catat mutasi kas, dan update status referral menjadi 'cair'.
     */
    public function cairkan(Referral $referral): void
    {
        $kas = KasBumdes::firstOrCreate(
            ['id_bumdes' => $referral->id_bumdes_pengaju],
            ['id_kas' => "KAS-{$referral->id_bumdes_pengaju}", 'saldo' => 0]
        );

        $kas->increment('saldo', 10000);

        KasMutasi::create([
            'id_kas'     => $kas->id_kas,
            'tipe'       => 'masuk',
            'jumlah'     => 10000,
            'sumber'     => 'referral',
            'keterangan' => "Pencairan referral {$referral->id_referral}",
            'tanggal'    => now(),
        ]);

        $referral->update(['status' => 'cair', 'tanggal_cair' => now()]);
    }
}
