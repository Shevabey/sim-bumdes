# Software Requirements Specification (SRS)
## Sistem Informasi Manajemen BUMDes Terintegrasi (SIM-BUMDes)

**Versi:** 1.0 | **Tanggal:** 16 September 2026
**Acuan:** BRD v2.0, PRD v1.0
**Catatan untuk AI Coding Agent:** Dokumen ini adalah spesifikasi teknis mengikat. Field, enum, dan aturan bisnis di sini harus diimplementasikan persis seperti tertulis kecuali ada instruksi lanjutan yang meng-override.

---

## 1. Pendahuluan

### 1.1 Tujuan
Menyediakan spesifikasi fungsional dan non-fungsional yang cukup detail agar dapat diimplementasikan langsung oleh developer maupun AI coding agent tanpa ambiguitas.

### 1.2 Definisi & Istilah
| Istilah | Definisi |
|---|---|
| BUMDes | Badan Usaha Milik Desa, entitas usaha di tingkat kelurahan |
| Unit Usaha | Sub-unit bisnis di bawah satu BUMDes (contoh: PAMDes, peternakan) |
| Koordinator Kecamatan | BUMDes yang ditunjuk sebagai pusat kecamatan (Sendangsari) |
| Referral | Mekanisme rujukan antar-BUMDes dengan insentif kas |
| RBAC | Role-Based Access Control |
| Kas BUMDes | Saldo dana BUMDes dari iuran & referral |

### 1.3 Ruang Lingkup
Lihat PRD Bab 4. Fase 1 mencakup 2 kelurahan (Sendangsari, Sendangrejo) di Kecamatan Minggir.

---

## 2. Deskripsi Umum Sistem

### 2.1 Arsitektur Tingkat Tinggi
Monolith Laravel dengan 2 permukaan UI:
1. **Panel Admin (Filament)** — untuk Super Admin, Pengawas/Penasihat/Direktur, Admin BUMDes, Sekretaris, Bendahara, Admin Unit.
2. **Portal Pengguna (Blade + Livewire custom, non-Filament)** — untuk Pelanggan/Pengguna (cek tagihan, upload bukti transfer).

Detail lihat `architecture.md`.

### 2.2 Kelas Pengguna & Karakteristik
Lihat tabel role lengkap di `architecture.md` Bab "RBAC & Role Matrix". Total 9 role sesuai BRD Bab 6 & 11.

### 2.3 Batasan Umum
- Tidak ada payment gateway (Fase 1).
- Tidak ada mode offline; UI harus tetap toleran koneksi lambat (retry otomatis pada submit gagal, tidak menghapus input form saat gagal submit).
- Semua ID entitas mengikuti skema prefix (Bab 6 dokumen ini).

---

## 3. Model Data & Aturan Bisnis Detail

### 3.1 Region
- Struktur self-referencing: `provinsi -> kota -> kecamatan -> kelurahan`.
- Field: `id_region (PK, string, format REG-xxxx)`, `level (enum: provinsi|kota|kecamatan|kelurahan)`, `parent_id (FK nullable ke region)`, `nama`, `is_koordinator (boolean, default false)`.
- Aturan: hanya 1 region level `kelurahan` dalam 1 `kecamatan` yang boleh `is_koordinator = true`.

### 3.2 BUMDes
- Field: `id_bumdes (PK, format BMD-{kode_kelurahan}-{urut3digit})`, `id_kelurahan (FK region)`, `nama_bumdes`, `status_aktif (boolean, default true)`, `tanggal_berdiri (date)`.
- Aturan bisnis:
  - Hanya **Super Admin** yang dapat mengubah `status_aktif`.
  - Saat `status_aktif = false`: seluruh unit di bawahnya otomatis read-only (tidak bisa transaksi baru), namun data historis tetap dapat dilihat.

### 3.3 Unit Usaha
- Field: `id_unit (PK, format UNT-{id_bumdes}-{kode_jenis}-{urut2digit})`, `id_bumdes (FK)`, `jenis_unit (enum: pamdes|peternakan|mitra_tani|sewa_mobil|sampah|custom)`, `nama_unit`, `skema_field (JSON — daftar field custom untuk transaksi unit ini)`, `status_aktif (boolean, default true)`.
- Aturan bisnis:
  - Hanya **Admin BUMDes** yang dapat menonaktifkan/mengaktifkan unit di BUMDes-nya.
  - `skema_field` contoh (PAMDes): `[{"key":"volume_air_m3","label":"Volume Air (m3)","type":"number"},{"key":"biaya_operasional","label":"Biaya Operasional","type":"currency"}]`.
  - Minimal 5 unit standar wajib tersedia sebagai template saat BUMDes baru dibuat: PAMDes, Peternakan, Mitra Tani, Sewa Mobil, Sampah. Admin BUMDes boleh menambah jenis unit baru (`jenis_unit = custom`).

### 3.4 Transaksi
- Field: `id_transaksi (PK, format TRX-{YYYYMMDD}-{urut6digit})`, `id_unit (FK)`, `tipe (enum: input|output)`, `jumlah (decimal)`, `detail (JSON, sesuai skema_field unit)`, `tanggal (date)`, `dicatat_oleh (FK akun)`.
- Aturan bisnis:
  - Untung-rugi unit = `SUM(tipe=input) - SUM(tipe=output)` dalam periode tertentu; dihitung on-the-fly atau via materialized summary table `unit_saldo_harian` untuk performa.
  - Hanya **Admin Unit** (unit terkait) dan **Bendahara** BUMDes yang dapat CRUD transaksi.

### 3.5 Pelanggan
- Field: `id_pelanggan (PK, format PLG-{id_unit}-{urut4digit})`, `id_unit (FK)`, `nama`, `kontak`, `id_akun (FK akun, nullable — diisi saat pelanggan diberi akses login)`, `status_aktif (boolean, default true)`.
- Aturan bisnis: Admin Unit dapat menonaktifkan pelanggan (berhenti berlangganan) tanpa menghapus data historis.

### 3.6 Tagihan
- Field: `id_tagihan (PK, format TAG-{YYYYMMDD}-{urut6digit})`, `id_pelanggan (FK)`, `id_unit (FK)`, `jumlah (decimal)`, `jatuh_tempo (date)`, `status (enum: belum_bayar|menunggu_verifikasi|lunas|ditolak)`, `metode (enum: tunai|transfer)`, `bukti_transfer_url (string nullable)`, `diverifikasi_oleh (FK akun, nullable)`, `tanggal_verifikasi (datetime nullable)`.
- State machine status:
  ```
  belum_bayar -> [tunai dicatat admin] -> lunas
  belum_bayar -> [pelanggan upload bukti] -> menunggu_verifikasi
  menunggu_verifikasi -> [admin unit approve] -> lunas
  menunggu_verifikasi -> [admin unit reject] -> ditolak -> (pelanggan upload ulang) -> menunggu_verifikasi
  ```

### 3.7 Iuran BUMDes
- Field: `id_iuran (PK, format IUR-{id_bumdes}-{YYYYMM})`, `id_bumdes (FK)`, `bulan_tahun (string YYYY-MM)`, `jumlah (decimal, default 50000)`, `status (enum: belum_bayar|lunas)`, `sumber_dana (enum: kas|luar_kas)`, `tanggal_bayar (datetime nullable)`, `diverifikasi_oleh (FK akun, nullable — admin BUMDes koordinator)`.
- Aturan bisnis: sistem men-generate record iuran otomatis via scheduled job setiap tanggal 1 tiap bulan untuk seluruh BUMDes berstatus aktif.

### 3.8 Kas BUMDes
- Field: `id_kas (PK, format KAS-{id_bumdes})`, `id_bumdes (FK, 1:1)`, `saldo (decimal)`, `riwayat_mutasi (relasi 1:N ke tabel kas_mutasi: id_mutasi, id_kas, tipe(masuk|keluar), jumlah, sumber(referral|iuran|lainnya), keterangan, tanggal)`.

### 3.9 Referral
- Field: `id_referral (PK, format REF-{id_bumdes_pengaju}-{6karakter_acak})`, `id_bumdes_pengaju (FK)`, `id_bumdes_penerima (FK, nullable sampai di-redeem)`, `kode_unik (string, unik, 6-8 karakter alfanumerik)`, `tanggal_generate (datetime)`, `tanggal_expired (datetime = tanggal_generate + 5 hari)`, `status (enum: aktif|terpakai|kedaluwarsa|pending|cair|gagal)`, `tanggal_redeem (datetime nullable)`, `batas_verifikasi (datetime = tanggal_redeem + 15 hari)`, `tanggal_cair (datetime nullable)`.
- State machine:
  ```
  aktif --(dipakai sebelum expired & belum pernah dipakai)--> terpakai -> pending
  aktif --(lewat 5 hari tanpa dipakai)--> kedaluwarsa (kode baru otomatis di-generate untuk BUMDes pengaju)
  pending --(syarat aktivitas terpenuhi dlm 15 hari)--> cair (kas +10000 ke BUMDes pengaju)
  pending --(15 hari lewat, syarat tidak terpenuhi)--> gagal
  ```
- "Syarat aktivitas minimal" = BUMDes penerima memiliki minimal 1 transaksi unit ATAU 1 pembayaran iuran tercatat setelah tanggal_redeem.
- Constraint: satu pasangan pengaju-penerima hanya boleh berhasil redeem 1 kali sepanjang riwayat, untuk mencegah abuse berulang.

### 3.10 Akun & RBAC
- Field: `id_akun (PK, format AKN-{urut6digit})`, `nama`, `username`, `password_hash`, `role (enum: super_admin|pengawas|penasihat|direktur|admin_bumdes|sekretaris|bendahara|admin_unit|pengguna)`, `id_bumdes (FK nullable)`, `id_unit (FK nullable, khusus role admin_unit — strict 1:1)`, `status_aktif (boolean, default true)`.
- Implementasi RBAC: gunakan `spatie/laravel-permission` dengan 9 role di atas sebagai Role, dan permission granular per modul (lihat `architecture.md` Bab RBAC Matrix untuk daftar permission).

### 3.11 Feedback/Catatan
- Field: `id_feedback (PK, format FB-{urut6digit})`, `dari_id_akun (FK)`, `ke_id_bumdes (FK)`, `ke_id_unit (FK nullable)`, `isi_catatan (text)`, `status_tindak_lanjut (enum: belum|sedang|selesai, default belum)`, `tanggal (datetime)`.
- Notifikasi real-time ke akun terkait BUMDes/unit saat feedback dibuat (gunakan Laravel Notification + broadcast channel).

### 3.12 Log Aktivitas
- Field: `id_log (PK)`, `id_akun (FK)`, `modul`, `aksi`, `detail (JSON)`, `waktu (datetime)`, `ip_address`.
- Diisi otomatis via `spatie/laravel-activitylog` pada setiap operasi create/update/delete/login/logout/verifikasi.

---

## 4. Kebutuhan Fungsional (Ringkasan Traceability ke BRD)

Seluruh 36 Functional Requirement (FR-01 s/d FR-36) pada BRD Bab 9 berlaku sebagai kebutuhan fungsional dokumen ini. Rincian aturan bisnis tiap FR dijabarkan pada Bab 3 di atas. Lihat `api-spec.json` untuk kontrak endpoint per FR.

## 5. Kebutuhan Non-Fungsional

| Kategori | Spesifikasi Teknis |
|---|---|
| Keamanan | Password di-hash bcrypt/argon2; CSRF protection Laravel default aktif; validasi file upload (mime: jpg/png/pdf, max 2MB) untuk bukti transfer; rate limiting login (5x/menit) |
| Performa | Query list menggunakan pagination (max 25 data/halaman); index pada seluruh foreign key dan kolom filter umum (status, tanggal) |
| Skalabilitas | Struktur ID prefix konsisten; tabel region self-referencing mendukung penambahan level tanpa migrasi ulang |
| Ketahanan Koneksi Lambat | Livewire wire:loading states wajib di semua form submit; implementasi retry otomatis (exponential backoff 3x percobaan) pada request gagal di sisi client |
| Audit | Setiap perubahan status (aktif/nonaktif, verifikasi tagihan, verifikasi referral) WAJIB tercatat di log aktivitas |

## 6. Skema ID (mengikat untuk implementasi)

Lihat BRD Bab 13 dan detail format di Bab 3 dokumen ini per entitas. AI coding agent WAJIB mengimplementasikan generator ID sesuai format yang tertulis di masing-masing sub-bab 3.1 - 3.12, bukan auto-increment integer polos untuk primary key yang exposed ke UI (auto-increment internal boleh dipakai sebagai internal surrogate key bila diperlukan, tapi ID yang ditampilkan/dirujuk harus mengikuti format prefix).

## 7. Referensi Dokumen Lain

- `PRD.md` — konteks produk, prioritas fitur, metrik keberhasilan
- `architecture.md` — arsitektur sistem, struktur folder, deployment
- `api-spec.json` — kontrak API/endpoint detail (OpenAPI 3.0)

---

## Riwayat Revisi

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0 | 16 September 2026 | Rilis awal SRS: model data 12 entitas, state machine, kebutuhan non-fungsional |
