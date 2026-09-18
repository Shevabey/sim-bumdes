# Architecture Document
## Sistem Informasi Manajemen BUMDes Terintegrasi (SIM-BUMDes)

**Versi:** 1.0 | **Tanggal:** 16 September 2026
**Acuan:** BRD v2.0 Bab 12 (Rekomendasi Teknis)

---

## 1. Gaya Arsitektur

**Modular Monolith** menggunakan Laravel — bukan microservices — karena skala fase percobaan kecil (2 kelurahan), tim kecil, dan kebutuhan deployment cepat/sederhana via Docker Compose di satu VPS.

```
┌─────────────────────────────────────────────────────────┐
│                     Laravel Application                  │
│                                                            │
│  ┌───────────────────────┐   ┌───────────────────────┐   │
│  │   Filament Admin       │   │  Portal Pengguna       │   │
│  │   (multi-panel)        │   │  (Blade + Livewire)    │   │
│  │  - super-admin panel   │   │  - Cek tagihan         │   │
│  │  - bumdes panel        │   │  - Upload bukti transfer│  │
│  │  - monitoring panel    │   │  - Lihat perkembangan  │   │
│  └───────────┬────────────┘   └───────────┬────────────┘   │
│              │                            │                │
│  ┌───────────▼────────────────────────────▼────────────┐   │
│  │            Service / Action Layer                    │   │
│  │  RegionService, BumdesService, UnitService,           │   │
│  │  TransaksiService, TagihanService, IuranService,      │   │
│  │  ReferralService, FeedbackService, ReportService      │   │
│  └───────────┬────────────────────────────────────────┘   │
│              │                                              │
│  ┌───────────▼────────────────────────────────────────┐   │
│  │         Eloquent Models + Policies (RBAC)            │   │
│  └───────────┬────────────────────────────────────────┘   │
│              │                                              │
│  ┌───────────▼────────────────────────────────────────┐   │
│  │              MySQL 8.x (via Docker)                  │   │
│  └───────────────────────────────────────────────────┘   │
│                                                            │
│  Queue Worker (Laravel Queue) -> Job: GenerateExcelReport, │
│  KirimNotifikasiFeedback, GenerateIuranBulanan (scheduled),│
│  CekBatasVerifikasiReferral (scheduled)                    │
└─────────────────────────────────────────────────────────┘
```

## 2. Tech Stack (sesuai BRD Bab 12)

| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 11.x, PHP 8.2+ |
| Admin Panel | FilamentPHP v3 (multi-panel) |
| Frontend Portal Pengguna | Blade + Livewire 3 |
| CSS | Tailwind CSS |
| Database | MySQL 8.x |
| Auth & RBAC | Laravel Sanctum (session) + spatie/laravel-permission |
| Log Audit | spatie/laravel-activitylog |
| Ekspor Excel | maatwebsite/excel |
| Notifikasi Real-time | Laravel Notification (database channel di Fase 1; broadcast/Reverb opsional Fase 2) |
| Queue | Laravel Queue (database driver) |
| Containerization | Docker + Docker Compose |
| Testing | PHPUnit / Pest |

## 3. Struktur Folder Laravel (Rekomendasi)

```
sim-bumdes/
├── app/
│   ├── Console/Commands/          # GenerateIuranBulanan, CekReferralExpired, dll
│   ├── Filament/
│   │   ├── SuperAdmin/Resources/  # BumdesResource, RegionResource, AccountResource, dll
│   │   ├── Bumdes/Resources/      # UnitResource, PelangganResource, TagihanResource, dll
│   │   └── Monitoring/Resources/  # DashboardWidget, FeedbackResource (read+create)
│   ├── Http/
│   │   ├── Controllers/Api/       # untuk endpoint di api-spec.json (portal pengguna)
│   │   ├── Livewire/              # komponen portal pengguna (CekTagihan, UploadBukti, dll)
│   │   └── Middleware/
│   ├── Models/                    # Region, Bumdes, Unit, Transaksi, Pelanggan, Tagihan,
│   │                               # IuranBumdes, KasBumdes, KasMutasi, Referral, Akun,
│   │                               # Feedback (nama model disesuaikan konvensi Laravel/Eloquent)
│   ├── Policies/                  # 1 policy per model utama, dicek via spatie permission
│   ├── Services/                  # business logic (lihat Bab 1 diagram)
│   ├── Notifications/             # FeedbackDiterima, TagihanBaru, VerifikasiDiperlukan
│   └── Jobs/                      # GenerateExcelReport, dll (queued)
├── database/
│   ├── migrations/
│   ├── seeders/                   # RegionSeeder (data Kec. Minggir), RoleSeeder, DemoDataSeeder
│   └── factories/
├── resources/
│   ├── views/livewire/            # portal pengguna
│   └── css/js
├── routes/
│   ├── web.php                    # portal pengguna + Filament auto-registered
│   └── api.php                    # sesuai api-spec.json
├── docker/
│   ├── php/Dockerfile
│   ├── nginx/default.conf
│   └── mysql/ (init scripts opsional)
├── docker-compose.yml
├── .env.example
└── tests/
```

## 4. RBAC & Role Matrix (Implementasi Teknis)

Gunakan `spatie/laravel-permission`. 9 Role (lihat SRS Bab 3.10), dengan Permission granular per modul, contoh penamaan:

```
region.view, region.create, region.update, region.delete
bumdes.view, bumdes.update, bumdes.toggle-status
unit.view, unit.create, unit.update, unit.toggle-status
pelanggan.view, pelanggan.create, pelanggan.update, pelanggan.toggle-status
transaksi.view, transaksi.create, transaksi.update
tagihan.view, tagihan.create, tagihan.verify, tagihan.pay-cash
iuran.view, iuran.pay, iuran.verify
referral.generate, referral.redeem, referral.view
akun.manage (scope dibatasi lewat Policy, bukan permission terpisah per BUMDes)
feedback.create, feedback.view, feedback.update-status
laporan.export
log-aktivitas.view (khusus super_admin)
dashboard.national (khusus 4 role nasional)
```

Setiap Model utama (Bumdes, Unit, Tagihan, dll.) punya **Policy** yang mengecek kombinasi permission + kepemilikan data (`id_bumdes`/`id_unit` milik akun yang login), bukan hanya permission generik. Contoh pseudocode `UnitPolicy@update`:

```php
public function update(Akun $akun, Unit $unit): bool
{
    if ($akun->hasRole('super_admin')) return true;
    if ($akun->hasRole('admin_bumdes') && $akun->id_bumdes === $unit->id_bumdes) return true;
    return false;
}
```

## 5. Struktur Filament (Multi-Panel)

Filament mendukung multi-panel dalam 1 instalasi. Rekomendasi 3 panel:

1. **`/admin`** (Panel: SuperAdmin) — akses: `super_admin`. Resource: Region, BUMDes, Akun (global), Log Aktivitas.
2. **`/bumdes`** (Panel: Bumdes) — akses: `admin_bumdes`, `sekretaris`, `bendahara`, `admin_unit`. Resource yang muncul difilter sesuai role (pakai `canViewAny()` per Resource + global scope `where id_bumdes = auth user's id_bumdes`).
3. **`/monitoring`** (Panel: Monitoring) — akses: `pengawas`, `penasihat`, `direktur`, `super_admin`. Berisi Dashboard widget (chart untung-rugi, status iuran) + Resource Feedback (create + list read-only ke semua BUMDes).

Portal pengguna (`pengguna` role) **tidak** memakai Filament — dibangun manual dengan Blade + Livewire agar UI dapat disederhanakan untuk pengguna awam (sesuai NFR Kemudahan Pengguna di BRD).

## 6. Alur Data Kritis (Ringkasan Teknis)

### 6.1 Referral (State Machine)
Diimplementasikan sebagai kolom `status` di tabel `referrals` + 2 Scheduled Command:
- `php artisan referral:expire-check` (jalan tiap jam) — set `aktif -> kedaluwarsa` jika lewat `tanggal_expired`, lalu generate kode baru otomatis.
- `php artisan referral:verify-check` (jalan tiap hari) — set `pending -> gagal` jika lewat `batas_verifikasi` tanpa aktivitas; jika ada aktivitas, set `pending -> cair` + insert `kas_mutasi` (+10000) ke `kas_bumdes` milik `id_bumdes_pengaju`.

### 6.2 Iuran Bulanan
Scheduled Command `php artisan iuran:generate-bulanan` (jalan tanggal 1 tiap bulan, via Laravel Scheduler) — insert record `iuran_bumdes` status `belum_bayar` untuk semua BUMDes `status_aktif = true`.

### 6.3 Notifikasi Real-time Feedback
Fase 1: gunakan Laravel Notification `database` channel + polling ringan di Livewire (`wire:poll.10s` pada komponen notifikasi) — cukup untuk skala kecil dan tidak butuh infrastruktur WebSocket tambahan. Fase 2 (bila diperlukan kecepatan lebih tinggi): upgrade ke `broadcast` channel dengan Laravel Reverb (self-hosted, tidak perlu layanan pihak ketiga berbayar seperti Pusher).

## 7. Deployment Architecture (Docker)

```
┌──────────────── Host VPS (2 vCPU / 4GB RAM) ────────────────┐
│                                                                │
│  ┌────────────┐   ┌─────────────┐   ┌───────────┐            │
│  │  webserver │──▶│    app      │──▶│    db     │            │
│  │  (nginx)   │   │ (php-fpm)   │   │  (mysql)  │            │
│  │  :80       │   │             │   │  :3306    │            │
│  └────────────┘   └──────┬──────┘   └───────────┘            │
│                          │                                    │
│                   ┌──────▼──────┐                             │
│                   │queue-worker │                             │
│                   │(php-fpm cli)│                             │
│                   └─────────────┘                             │
│  Volumes: app-storage (uploads bukti transfer), db-data       │
└────────────────────────────────────────────────────────────┘
```

Detail file konfigurasi Docker disediakan terpisah (`docker-compose.yml`, `Dockerfile`, `nginx/default.conf`).

**Catatan Environment:** Docker di atas dipakai untuk **deployment/uji coba lapangan** (VPS). Untuk **development sehari-hari**, tim memakai **Laragon di Windows** (PHP + MySQL + Composer lokal tanpa container) agar lebih ringan dan cepat saat iterasi kode. Lihat `SETUP_GUIDE.md` untuk langkah setup Laragon secara lengkap. Konfigurasi `.env` berbeda antara kedua environment ini (`DB_HOST=127.0.0.1` di Laragon vs `DB_HOST=db` di Docker).

## 8. Keamanan

- Seluruh route Filament & Livewire dilindungi middleware `auth` + Policy per Resource/aksi.
- File upload bukti transfer disimpan di `storage/app/public/bukti-transfer`, divalidasi mime-type & ukuran maksimum 2MB, nama file di-hash (bukan nama asli) untuk mencegah path traversal/collision.
- Rate limiting login: 5 percobaan/menit per IP (Laravel `throttle` middleware).
- `.env` tidak pernah di-commit; gunakan `.env.example` sebagai template.

## 9. Konvensi Penamaan & ID (Ringkas — detail di SRS Bab 3 & 6)

Primary key yang **ditampilkan ke pengguna** memakai format prefix (REG-, BMD-, UNT-, TRX-, TAG-, IUR-, REF-, AKN-, PLG-, FB-). Disarankan disimpan sebagai kolom string terindeks unik (`id_xxx VARCHAR(30) UNIQUE`), sementara Laravel tetap boleh punya auto-increment `id` internal (bigint) sebagai primary key fisik untuk performa join — kolom prefix menjadi *business key* yang di-expose ke API/UI.

## 10. Referensi Dokumen Lain

- `SRS.md` — detail model data & aturan bisnis per entitas
- `PRD.md` — konteks produk & prioritas fitur
- `api-spec.json` — kontrak endpoint (OpenAPI 3.0)

---

## Riwayat Revisi

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0 | 16 September 2026 | Rilis awal arsitektur: modular monolith Laravel, multi-panel Filament, deployment Docker |
| 1.1 | 16 September 2026 | Tambah catatan environment development (Laragon, tanpa Docker) vs environment deployment (Docker) — lihat SETUP_GUIDE.md |
