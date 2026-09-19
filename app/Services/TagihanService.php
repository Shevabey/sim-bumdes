<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\Tagihan;

class TagihanService
{
    /**
     * Verifikasi tagihan oleh admin unit atau bendahara.
     * Status berubah menjadi 'lunas' (approve) atau 'ditolak' (reject).
     *
     * @param  Tagihan  $tagihan      Tagihan yang akan diverifikasi
     * @param  Akun     $verifikator  Akun yang melakukan verifikasi
     * @param  bool     $approve      true = lunas, false = ditolak
     * @param  string|null $catatan   Catatan opsional dari verifikator
     */
    public function verifikasi(Tagihan $tagihan, Akun $verifikator, bool $approve, ?string $catatan = null): Tagihan
    {
        $tagihan->update([
            'status'              => $approve ? 'lunas' : 'ditolak',
            'diverifikasi_oleh'   => $verifikator->id_akun,
            'tanggal_verifikasi'  => now(),
        ]);

        return $tagihan;
    }
}
