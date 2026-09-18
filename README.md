# SIM-BUMDes

### Sistem Informasi Manajemen BUMDes Terintegrasi

Sistem pencatatan keuangan, manajemen unit usaha, iuran & referral antar-BUMDes, serta dashboard monitoring untuk BUMDes se-Kecamatan Minggir — dimulai dari fase percobaan di Kelurahan Sendangsari dan Sendangrejo.

Dibangun dengan **Laravel 11**, **FilamentPHP 3** (admin panel), **Livewire 3** (portal pengguna), dan **MySQL 8**.

---

## Daftar Isi

- [Dokumen Acuan](#dokumen-acuan)
- [Prasyarat](#prasyarat)
- [Cara Clone & Install (Development — Windows/Laragon)](#cara-clone--install-development--windowslaragon)
- [Menjalankan Project](#menjalankan-project)
- [Struktur Folder](#struktur-folder)
- [Package Utama](#package-utama)
- [Deployment (Docker)](#deployment-docker)
- [Kontribusi & Update Dokumen](#kontribusi--update-dokumen)

---

## Dokumen Acuan

Seluruh spesifikasi produk dan teknis ada di folder [`docs/`](./docs):

| Dokumen                | Isi                                                                                                        |
| ---------------------- | ---------------------------------------------------------------------------------------------------------- |
| `PRD.md`               | Product Requirements — tujuan produk, persona, prioritas fitur, metrik keberhasilan                        |
| `SRS.md`               | Software Requirements Specification — spesifikasi teknis mengikat: field, enum, state machine tiap entitas |
| `architecture.md`      | Arsitektur sistem, struktur folder, RBAC, deployment                                                       |
| `api-spec.json`        | Kontrak API format OpenAPI 3.0                                                                             |
| `database-schema.dbml` | Skema database — import ke [dbdiagram.io](https://dbdiagram.io) untuk lihat ERD visual                     |
| `SETUP_GUIDE.md`       | Panduan instalasi manual bertahap (Windows + Laragon)                                                      |

**Wajib dibaca sebelum mengembangkan fitur baru** — terutama `SRS.md` untuk aturan bisnis dan `architecture.md` untuk struktur kode. Jika menggunakan AI coding agent (Claude Code, dsb.), arahkan agent membaca folder `docs/` ini sebagai konteks awal.

---

## Prasyarat

- [Laragon Full](https://laragon.org/download/) (bundel PHP 8.2+, Composer, MySQL, Node.js)
- Git
- (Opsional, untuk deployment) Docker Desktop

---

## Cara Clone & Install (Development — Windows/Laragon)

```powershell
cd C:\laragon\www
git clone <URL_REPOSITORY_ANDA> sim-bumdes
cd sim-bumdes
composer install
copy .env.example .env
php artisan key:generate
```

Buat database `sim_bumdes` lewat HeidiSQL (bundel Laragon), lalu sesuaikan kredensial di `.env` bila perlu (lihat `docs/SETUP_GUIDE.md` Fase 2 untuk detail lengkap). Lanjutkan:

```powershell
php artisan migrate
php artisan storage:link
npm install
npm run build
```

Untuk **instalasi dari nol** (belum ada kode sama sekali, mulai dari `composer create-project`), ikuti `docs/SETUP_GUIDE.md` secara lengkap dari Fase 0.

---

## Menjalankan Project

Karena project berada di `C:\laragon\www\sim-bumdes`, Laragon otomatis membuatkan virtual host:

```
http://sim-bumdes.test          → Portal Pengguna
http://sim-bumdes.test/admin    → Panel Filament (Super Admin / Admin BUMDes / dst)
```

Pastikan Laragon dalam keadaan **Start All** (Apache/Nginx + MySQL aktif).

Untuk development asset dengan hot-reload, jalankan di terminal terpisah:

```powershell
npm run dev
```

---

## Struktur Folder

```
sim-bumdes/
├── app/
│   ├── Filament/{SuperAdmin,Bumdes,Monitoring}/Resources/
│   ├── Http/Livewire/          # Portal pengguna
│   ├── Models/
│   ├── Policies/
│   ├── Services/               # Business logic per modul
│   ├── Notifications/
│   └── Jobs/
├── database/migrations/
├── docs/                       # Dokumen acuan (lihat tabel di atas)
├── docker/                     # Konfigurasi Docker (dipakai saat deployment)
├── docker-compose.yml
└── routes/{web.php,api.php}
```

Detail lengkap ada di `docs/architecture.md` Bab 3.

---

## Package Utama

| Kebutuhan    | Package                         |
| ------------ | ------------------------------- |
| Admin Panel  | `filament/filament`             |
| RBAC         | `spatie/laravel-permission`     |
| Log Audit    | `spatie/laravel-activitylog`    |
| Ekspor Excel | `maatwebsite/excel`             |
| Frontend     | Blade + Livewire + Tailwind CSS |

Lihat `docs/SETUP_GUIDE.md` Fase 5 untuk perintah instalasi masing-masing.

---

## Deployment (Docker)

Development sehari-hari memakai Laragon (tanpa Docker). Saat siap deploy ke VPS untuk uji coba lapangan, gunakan `docker-compose.yml` dan folder `docker/` yang sudah disertakan — jalankan:

```bash
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
```

Detail lengkap di `docs/architecture.md` Bab 7 dan `docs/SETUP_GUIDE.md` Fase 9.

---

## Kontribusi & Update Dokumen

Dokumen di folder `docs/` (`PRD.md`, `SRS.md`, `architecture.md`, `api-spec.json`) **hidup dan dapat berubah** seiring pengembangan. Setiap dokumen memiliki bagian **"Riwayat Revisi"** di bagian bawah — setiap kali ada perubahan requirement atau desain teknis, catat versinya di sana agar seluruh tim (termasuk AI coding agent) selalu mengacu pada versi terbaru dan tahu apa yang berubah.

Alur update dokumen yang disarankan:

1. Diskusikan perubahan requirement/desain.
2. Update dokumen terkait (`PRD.md`/`SRS.md`/`architecture.md`/`api-spec.json`) + tambah baris baru di tabel Riwayat Revisi.
3. Commit perubahan dokumen terpisah dari commit kode, dengan pesan commit yang jelas (misal `docs: update SRS - tambah field X pada modul Y`).
