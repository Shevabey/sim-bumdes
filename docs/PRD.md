# Product Requirements Document (PRD)
## Sistem Informasi Manajemen BUMDes Terintegrasi (SIM-BUMDes)

**Versi:** 1.0
**Tanggal:** 16 September 2026
**Sumber acuan:** Business Requirement Document (BRD) v2.0
**Fase:** Fase 1 - Percobaan (Kelurahan Sendangsari & Sendangrejo, Kecamatan Minggir)

---

## 1. Latar Belakang & Masalah

Pengelolaan BUMDes di banyak kelurahan masih manual/semi-manual, menyulitkan:
- Pemantauan kinerja keuangan tiap unit usaha
- Transparansi & akuntabilitas ke pengawas/penasihat/direktur
- Koordinasi antar-BUMDes dalam satu kecamatan (iuran, referral)
- Verifikasi pembayaran pelanggan yang tersebar di berbagai unit usaha

## 2. Tujuan Produk

1. Digitalisasi pencatatan keuangan per unit usaha (input-output, untung-rugi) dengan skema fleksibel per jenis unit.
2. Struktur data wilayah berjenjang (provinsi > kota > kecamatan > kelurahan) yang scalable.
3. Otomasi iuran bulanan antar-BUMDes + mekanisme referral untuk pertumbuhan jaringan.
4. Kontrol akses berjenjang (9 role) sesuai kewenangan masing-masing.
5. Dashboard monitoring real-time + mekanisme feedback dari level pengawasan ke pengurus BUMDes.
6. Laporan keuangan yang dapat diekspor ke Excel.

## 3. Target Pengguna (Personas)

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| Super Admin | Pengelola sistem tingkat nasional/kecamatan | Kontrol penuh, monitoring seluruh akun & BUMDes |
| Pengawas/Penasihat/Direktur | Pemangku kepentingan tingkat kecamatan | Visibilitas real-time + kemampuan memberi arahan |
| Admin BUMDes | Pengurus utama BUMDes per kelurahan | Kelola unit, iuran, referral |
| Sekretaris | Staf administratif BUMDes | Kelola dokumen, pelanggan, tagihan non-keuangan |
| Bendahara | Staf keuangan BUMDes | Kelola seluruh transaksi keuangan |
| Admin Unit | Operator harian unit usaha | Input transaksi & tagihan cepat, meski koneksi lambat |
| Pengguna/Pelanggan | Warga desa pengguna layanan unit | Cek tagihan, bayar, upload bukti transfer |

## 4. Ruang Lingkup Produk (Fase 1)

### In-Scope
- Manajemen region, BUMDes, unit usaha (+ toggle aktif/nonaktif berjenjang)
- Pencatatan transaksi per unit dengan skema fleksibel (JSON schema per jenis unit)
- Tagihan pelanggan: manual + upload bukti transfer + verifikasi admin unit
- Iuran bulanan Rp50.000/BUMDes + referral antar-BUMDes (kode 5 hari, verifikasi aktivitas 15 hari)
- RBAC 9 role sesuai matriks akses BRD Bab 11
- Dashboard monitoring + modul feedback/catatan + notifikasi real-time
- Log aktivitas akun (audit trail)
- Ekspor laporan ke Excel
- Web app, dioptimalkan untuk koneksi lambat (retry otomatis, anti-kehilangan data form)

### Out-of-Scope (Fase 1 - ditunda)
- Payment gateway (pembayaran otomatis online)
- Mode offline/PWA dengan sinkronisasi
- Aplikasi mobile native

## 5. Fitur & Prioritas (MoSCoW)

| Fitur | Prioritas | Modul BRD Terkait |
|---|---|---|
| CRUD Region berjenjang dengan ID unik | Must | FR-01 s/d FR-03 |
| CRUD BUMDes + toggle aktif/nonaktif | Must | FR-04, FR-08 |
| CRUD Unit Usaha + skema fleksibel + toggle aktif/nonaktif | Must | FR-05, FR-06, FR-09 |
| Pencatatan transaksi & hitung untung-rugi otomatis | Must | FR-07 |
| CRUD Pelanggan + toggle aktif/nonaktif | Must | FR-10 |
| Tagihan + upload bukti transfer + verifikasi | Must | FR-11 s/d FR-15 |
| Iuran bulanan otomatis + pembayaran dari kas | Must | FR-16, FR-17 |
| Referral: generate, redeem, validasi, pencairan kas | Must | FR-18 s/d FR-22 |
| RBAC & manajemen akun | Must | FR-23 s/d FR-27 |
| Laporan & ekspor Excel | Must | FR-28 |
| Dashboard monitoring real-time | Must | FR-29, FR-30 |
| Log audit & log aktivitas akun | Must | FR-31, FR-32 |
| Modul Feedback/Catatan + notifikasi real-time | Should | FR-33 s/d FR-36 |
| Optimisasi UX koneksi lambat (retry, anti-kehilangan form) | Should | NFR |
| Admin panel berbasis Filament | Should | Rekomendasi Teknis |

## 6. Metrik Keberhasilan (Fase Percobaan)

- Seluruh 5 unit usaha di 2 BUMDes berhasil mencatat transaksi rutin selama minimal 1 bulan penuh tanpa kendala kritis.
- Minimal 1 siklus referral berhasil diuji end-to-end (generate -> redeem -> verifikasi -> pencairan/gagal).
- Waktu verifikasi bukti transfer oleh admin unit rata-rata < 24 jam.
- Tidak ada kehilangan data input akibat koneksi lambat selama uji coba (0 insiden tercatat).
- Laporan Excel dapat diekspor tanpa error untuk seluruh unit dan BUMDes yang aktif.

## 7. Asumsi & Batasan

- Tim pengembang memiliki keahlian PHP/Laravel; stack lain tidak dipertimbangkan.
- Koneksi internet di lokasi (Sendangsari, Sendangrejo) tergolong terbatas/tidak stabil.
- Tidak ada anggaran untuk payment gateway berbayar pada fase ini.
- Server/hosting akan ditentukan kemudian; arsitektur harus portable (containerized) agar tidak terikat 1 penyedia.

## 8. Pertanyaan Terbuka

1. Format & periode pasti laporan Excel (mingguan/bulanan).
2. Field detail pencatatan transaksi tiap 5 jenis unit usaha.
3. Pola resmi kode wilayah (REG-xxxx): mengikuti Kemendagri atau kode internal.
4. Kanal notifikasi real-time: WebSocket (Laravel Reverb) vs polling sederhana untuk fase awal.

---

## Riwayat Revisi

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0 | 16 September 2026 | Rilis awal PRD berdasarkan BRD v2.0 |
