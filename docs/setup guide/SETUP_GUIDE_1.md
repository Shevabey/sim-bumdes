# Panduan Instalasi Manual — SIM-BUMDes
## Laravel + FilamentPHP di Windows dengan Laragon

**Versi Panduan:** 2.0 (revisi: setup manual Laragon dulu, Docker dipindah ke fase akhir/opsional)
**Prasyarat OS:** Windows dengan [Laragon](https://laragon.org/download/) (Full version — sudah termasuk PHP, MySQL, Composer, Nginx/Apache)

Ikuti berurutan dari **FASE 0** sampai **FASE 8**. Setiap fase punya cara verifikasi sebelum lanjut ke fase berikutnya — jangan lompat fase.

---

## FASE 0 — Persiapan Laragon

1. Install **Laragon Full** dari laragon.org (sudah bundel PHP 8.2+, Composer, MySQL, Node.js opsional).
2. Buka Laragon, klik **Start All** (menyalakan Apache/Nginx + MySQL).
3. Cek versi PHP & Composer sudah aktif — buka **Terminal** dari Laragon (klik kanan tray icon Laragon → Terminal), lalu jalankan:

```powershell
php -v
composer -v
mysql --version
```

Pastikan PHP versi **8.2 atau lebih baru**. Kalau versi PHP masih lama, buka Laragon → menu **PHP** → pilih versi 8.2+ (download dulu lewat Laragon jika belum ada).

**Verifikasi Fase 0:** ketiga command di atas menampilkan versi tanpa error.

---

## FASE 1 — Buat Project Laravel

Laragon secara default menyimpan semua project di folder `C:\laragon\www`. Buka terminal Laragon, arahkan ke folder tersebut:

```powershell
cd C:\laragon\www
composer create-project laravel/laravel sim-bumdes "11.*"
cd sim-bumdes
```

Karena Laragon otomatis mendeteksi folder di `www/`, project ini akan bisa diakses lewat **http://sim-bumdes.test** (Laragon auto-virtual-host) begitu Apache/Nginx aktif — tanpa perlu setting host manual.

**Verifikasi Fase 1:** buka browser ke `http://sim-bumdes.test` — muncul halaman selamat datang Laravel.

**Commit setelah Fase 1** (inisialisasi repo Git terlebih dulu jika belum: `git init`):
```
chore: inisialisasi project Laravel 11 (fresh install)
```

---

## FASE 2 — Buat Database & Konfigurasi `.env`

### 2.1 Buat database lewat HeidiSQL (bundel Laragon)

1. Buka Laragon → klik menu **Database** (ikon database) → otomatis membuka HeidiSQL yang sudah tersambung ke MySQL lokal.
2. Klik kanan pada koneksi → **Create New** → **Database**.
3. Nama database: `sim_bumdes`, Collation: `utf8mb4_unicode_ci` → OK.

### 2.2 Edit file `.env` di root project (`sim-bumdes/.env`)

```env
APP_NAME="SIM-BUMDes"
APP_ENV=local
APP_URL=http://sim-bumdes.test
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sim_bumdes
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public
```

Catatan: default MySQL Laragon memakai user `root` tanpa password. Sesuaikan jika Anda sudah mengubah kredensial MySQL Laragon.

### 2.3 Generate APP_KEY

```powershell
php artisan key:generate
```

**Verifikasi Fase 2:** jalankan `php artisan migrate:status` — tidak error koneksi database (boleh menampilkan "no migrations" jika belum ada tabel).

**Commit setelah Fase 2:**
```
chore(config): konfigurasi koneksi database MySQL lokal (Laragon)
```
*(Pastikan `.env` masuk `.gitignore` — yang di-commit cukup `.env.example` bila sudah dibuat)*

---

## FASE 3 — Migrasi Awal & Storage Link

```powershell
php artisan migrate
php artisan storage:link
```

`storage:link` diperlukan agar file upload (bukti transfer, dsb.) nanti bisa diakses publik via `public/storage`.

**Verifikasi Fase 3:** tabel default Laravel (`users`, `sessions`, `cache`, `jobs`, dst) sudah muncul di database `sim_bumdes` — cek lewat HeidiSQL.

**Commit setelah Fase 3:**
```
chore: jalankan migration default Laravel dan setup storage link
```

---

## FASE 4 — Install FilamentPHP

```powershell
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

Saat diminta Panel ID, isi: `admin`.

Buat user pertama untuk login ke panel:

```powershell
php artisan make:filament-user
```

Isi nama, email, dan password sesuai keinginan.

**Verifikasi Fase 4:** buka `http://sim-bumdes.test/admin`, muncul halaman login Filament, dan berhasil login dengan akun yang baru dibuat.

**Commit setelah Fase 4:**
```
feat: install FilamentPHP dan setup panel admin awal
```

---

## FASE 5 — Install Package Pendukung (sesuai BRD Bab 12)

Jalankan satu per satu, verifikasi tiap package tidak error sebelum lanjut ke berikutnya:

### 5.1 RBAC — Role & Permission

```powershell
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 5.2 Log Audit / Activity Log

```powershell
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### 5.3 Ekspor Excel

```powershell
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### 5.4 (Opsional, siapkan sejak awal) Notifikasi Database

Tidak perlu package tambahan — Laravel Notification database channel sudah bawaan framework. Cukup pastikan tabel notifikasi tersedia:

```powershell
php artisan notifications:table
php artisan migrate
```

**Ringkasan package Fase 5 (sesuai rekomendasi BRD Bab 12.5):**

| Kebutuhan | Package | Status |
|---|---|---|
| Admin Panel | filament/filament | ✅ Fase 4 |
| RBAC | spatie/laravel-permission | ✅ 5.1 |
| Log Audit | spatie/laravel-activitylog | ✅ 5.2 |
| Ekspor Excel | maatwebsite/excel | ✅ 5.3 |
| Notifikasi | Laravel Notification (bawaan) | ✅ 5.4 |

**Verifikasi Fase 5:** `composer show` menampilkan ketiga package di atas tanpa error, dan `php artisan migrate:status` menunjukkan migration baru dari masing-masing package berstatus `Ran`.

**Commit setelah Fase 5:**
```
feat: install package RBAC, activity log, dan export Excel

Menambahkan spatie/laravel-permission, spatie/laravel-activitylog,
dan maatwebsite/excel sesuai rekomendasi teknis BRD Bab 12.5.
```

---

## FASE 6 — Build Asset Frontend (Tailwind + Livewire)

Livewire ikut terpasang otomatis lewat Filament. Untuk portal pengguna custom, install Tailwind bila belum ada (Laravel 11 default sudah include Vite + minimal CSS):

```powershell
npm install
npm run build
```

Untuk mode pengembangan dengan hot-reload saat edit CSS/JS:

```powershell
npm run dev
```

Biarkan perintah `npm run dev` berjalan di terminal terpisah selama sesi development berlangsung.

**Verifikasi Fase 6:** `npm run build` selesai tanpa error, folder `public/build` terbentuk.

**Commit setelah Fase 6:**
```
chore: build asset frontend awal (Tailwind + Vite)
```

---

## FASE 7 — Struktur Folder Modul & Dokumen Acuan

### 7.1 Buat folder modul bisnis sesuai `architecture.md`

```powershell
mkdir app\Services
mkdir app\Filament\SuperAdmin\Resources
mkdir app\Filament\Bumdes\Resources
mkdir app\Filament\Monitoring\Resources
mkdir app\Http\Livewire
mkdir app\Notifications
mkdir app\Jobs
mkdir docs
```

### 7.2 Salin dokumen acuan ke folder `docs/`

Salin 5 file berikut ke dalam `sim-bumdes\docs\`:
- `PRD.md`
- `SRS.md`
- `architecture.md`
- `api-spec.json`
- `database-schema.dbml`

Dokumen-dokumen ini menjadi **konteks wajib dibaca** sebelum AI coding agent (atau developer manapun) mulai membuat migration, model, dan Filament Resource — supaya penamaan field, ID, dan aturan bisnis konsisten.

**Verifikasi Fase 7:** folder `docs/` berisi 5 file di atas, dan struktur folder `app/` sudah sesuai `architecture.md` Bab 3.

**Commit setelah Fase 7:**
```
docs: tambah dokumen acuan (PRD, SRS, architecture, api-spec, dbml)

Menyusun struktur folder modul bisnis awal (Services, Filament/*,
Livewire, Notifications, Jobs) sesuai architecture.md Bab 3.
```

---

## FASE 8 — Checklist Verifikasi Akhir Sebelum Mulai Coding Modul Bisnis

| # | Cek | Cara Verifikasi | Status |
|---|---|---|---|
| 1 | Laravel jalan | `http://sim-bumdes.test` tampil | ☐ |
| 2 | Database tersambung | `php artisan migrate:status` tanpa error | ☐ |
| 3 | Filament panel jalan | `http://sim-bumdes.test/admin` bisa login | ☐ |
| 4 | RBAC siap | Tabel `roles`, `permissions` ada di database | ☐ |
| 5 | Log audit siap | Tabel `activity_log` ada di database | ☐ |
| 6 | Excel export siap | `composer show maatwebsite/excel` menampilkan versi terpasang | ☐ |
| 7 | Asset frontend ter-build | Folder `public/build` ada | ☐ |
| 8 | Struktur folder modul siap | `app/Services`, `app/Filament/*` ada | ☐ |
| 9 | Dokumen acuan tersalin | `docs/` berisi 5 file (PRD, SRS, architecture, api-spec, dbml) | ☐ |

Setelah semua tercentang, project siap masuk ke tahap pembuatan migration & model 12 entitas sesuai `SRS.md` Bab 3 — lanjutkan ke **`SETUP_GUIDE_LANJUTAN.md`** (Fase 9 dan seterusnya).

### Rekap Commit Fase 0-8

Fase 0 tidak perlu commit (belum ada project/repo). Jika sejauh ini belum sempat commit bertahap, rangkap saja jadi 1 commit besar per kelompok berikut (atau ikuti commit per fase yang sudah dicantumkan di atas bila mau riwayat lebih rinci):

| Opsi | Pesan Commit |
|---|---|
| **Rangkap semua (Fase 1-8 jadi 1 commit)** | `feat: setup awal project Laravel + Filament + package pendukung sesuai BRD\n\nFase 1-8: instalasi Laravel 11, konfigurasi database Laragon, migration\ndefault, FilamentPHP + user admin pertama, package RBAC/activity-log/\nexcel, build asset frontend, struktur folder modul, dan dokumen acuan\n(PRD/SRS/architecture/api-spec/dbml) di folder docs/.` |
| **Rangkap per kelompok kecil (4 commit)** | 1) `chore: inisialisasi project Laravel 11 + konfigurasi database` (Fase 1-3)<br>2) `feat: install FilamentPHP + user admin pertama` (Fase 4)<br>3) `feat: install package RBAC, activity log, export Excel` (Fase 5-6)<br>4) `docs: tambah dokumen acuan dan struktur folder modul` (Fase 7) |

Pilih salah satu opsi sesuai kebiasaan tim — untuk riwayat Git yang lebih mudah ditelusuri saat debugging, opsi commit per fase (sudah tercantum di masing-masing fase di atas) lebih disarankan dibanding merangkap semua jadi 1 commit besar.

---

## FASE 9 (NANTI / OPSIONAL) — Containerize dengan Docker untuk Deployment

Fase ini **tidak perlu dikerjakan sekarang** — dilakukan belakangan saat project sudah siap dipindahkan ke server/VPS untuk uji coba di lapangan (Sendangsari & Sendangrejo), bukan untuk development sehari-hari di Windows/Laragon.

File yang dibutuhkan (`docker-compose.yml`, `docker/php/Dockerfile`, `docker/nginx/default.conf`, `.env.example` untuk Docker) sudah disiapkan sebelumnya dan tetap berlaku tanpa perubahan — tinggal dipakai saat waktunya deploy. Ringkasan services: `app` (PHP-FPM), `webserver` (Nginx), `db` (MySQL), `queue-worker`, `scheduler`, `node` (build asset). Detail lengkap ada di `architecture.md` Bab 7 dan file-file Docker yang sudah diberikan sebelumnya.

---

## Perintah Harian yang Sering Dipakai (Referensi Cepat — Laragon)

```powershell
php artisan serve                          # alternatif jalankan server manual (jika tidak pakai domain .test Laragon)
php artisan migrate                        # jalankan migration baru
php artisan migrate:fresh --seed           # reset total database + jalankan seeder
php artisan make:model NamaModel -mf       # buat model + migration + factory sekaligus
php artisan make:filament-resource NamaModel   # generate CRUD Filament otomatis
php artisan make:filament-panel bumdes     # buat panel Filament baru (misal panel khusus BUMDes)
php artisan queue:work                     # jalankan queue worker manual (untuk testing lokal)
php artisan schedule:work                  # jalankan scheduler manual (untuk testing lokal, gantikan cron)
npm run dev                                # mode development asset dengan hot-reload
```

---

## Langkah Berikutnya Setelah Fase 8 Selesai

1. Buat migration untuk 12 entitas sesuai `SRS.md` Bab 3 (urutan disarankan: Region → BUMDes → Unit Usaha → Akun → Pelanggan → Transaksi → Tagihan → Iuran BUMDes → Kas BUMDes + Kas Mutasi → Referral → Feedback → Log Aktivitas via Spatie).
2. Buat Eloquent Model + relasi Eloquent untuk masing-masing tabel.
3. Buat Seeder wilayah (5 kelurahan Kecamatan Minggir) dan Role/Permission awal (9 role sesuai SRS Bab 3.10).
4. Buat Filament Resource dimulai dari Region & BUMDes, baru menyusul modul lain sesuai prioritas MoSCoW di `PRD.md` Bab 5.
