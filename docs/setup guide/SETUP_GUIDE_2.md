# Panduan Instalasi Lanjutan — SIM-BUMDes
## Fase 9-14: Migration, Model, Seeder, RBAC, Filament Resource

**Prasyarat:** `SETUP_GUIDE.md` Fase 0-8 sudah selesai dan seluruh checklist Fase 8 tercentang.
**Acuan field/aturan bisnis:** `SRS.md` Bab 3, `database-schema.dbml`

Urutan fase di bawah **wajib berurutan** karena tiap tabel punya foreign key ke tabel sebelumnya.

---

## FASE 9 — Migration (13 Tabel)

Buat migration satu per satu sesuai urutan dependency (parent dulu, baru child):

```powershell
php artisan make:migration create_region_table
php artisan make:migration create_bumdes_table
php artisan make:migration create_unit_usaha_table
php artisan make:migration create_akun_table
php artisan make:migration create_pelanggan_table
php artisan make:migration create_transaksi_table
php artisan make:migration create_tagihan_table
php artisan make:migration create_iuran_bumdes_table
php artisan make:migration create_kas_bumdes_table
php artisan make:migration create_kas_mutasi_table
php artisan make:migration create_referral_table
php artisan make:migration create_feedback_table
```

(`log_aktivitas` **tidak perlu** dibuat manual — sudah otomatis tersedia dari `spatie/laravel-activitylog` yang di-migrate di Fase 5.2 pada `SETUP_GUIDE.md`.)

### 9.1 Isi tiap file migration

Gunakan `id_xxx` (string, format prefix) sebagai **primary key** langsung (bukan auto-increment terpisah), sesuai `architecture.md` Bab 9. Contoh isi lengkap untuk 3 migration pertama sebagai referensi pola — sisanya mengikuti pola yang sama sesuai kolom di `SRS.md` Bab 3 / `database-schema.dbml`.

**`database/migrations/..._create_region_table.php`**
```php
public function up(): void
{
    Schema::create('region', function (Blueprint $table) {
        $table->string('id_region', 30)->primary();
        $table->enum('level', ['provinsi', 'kota', 'kecamatan', 'kelurahan']);
        $table->string('parent_id', 30)->nullable();
        $table->string('nama', 150);
        $table->boolean('is_koordinator')->default(false);
        $table->timestamps();

        $table->foreign('parent_id')->references('id_region')->on('region')->nullOnDelete();
    });
}
```

**`..._create_bumdes_table.php`**
```php
public function up(): void
{
    Schema::create('bumdes', function (Blueprint $table) {
        $table->string('id_bumdes', 30)->primary();
        $table->string('id_kelurahan', 30);
        $table->string('nama_bumdes', 150);
        $table->boolean('status_aktif')->default(true);
        $table->date('tanggal_berdiri')->nullable();
        $table->timestamps();

        $table->foreign('id_kelurahan')->references('id_region')->on('region');
    });
}
```

**`..._create_unit_usaha_table.php`**
```php
public function up(): void
{
    Schema::create('unit_usaha', function (Blueprint $table) {
        $table->string('id_unit', 30)->primary();
        $table->string('id_bumdes', 30);
        $table->enum('jenis_unit', ['pamdes', 'peternakan', 'mitra_tani', 'sewa_mobil', 'sampah', 'custom']);
        $table->string('nama_unit', 150);
        $table->json('skema_field')->nullable();
        $table->boolean('status_aktif')->default(true);
        $table->timestamps();

        $table->foreign('id_bumdes')->references('id_bumdes')->on('bumdes');
    });
}
```

**Untuk 9 tabel sisanya, ikuti kolom persis seperti di `database-schema.dbml`:**

| Migration | Kolom Kunci yang Wajib Ada |
|---|---|
| `create_akun_table` | `id_akun` (PK), `username` (unique), `password_hash`, `role` (enum 9 nilai — lihat SRS 3.10), `id_bumdes` (FK nullable), `id_unit` (FK nullable), `status_aktif` |
| `create_pelanggan_table` | `id_pelanggan` (PK), `id_unit` (FK), `id_akun` (FK nullable), `status_aktif` |
| `create_transaksi_table` | `id_transaksi` (PK), `id_unit` (FK), `tipe` (enum input/output), `jumlah` (decimal 15,2), `detail` (json), `dicatat_oleh` (FK ke akun) |
| `create_tagihan_table` | `id_tagihan` (PK), `id_pelanggan` (FK), `id_unit` (FK), `status` (enum 4 nilai), `metode` (enum nullable), `bukti_transfer_url`, `diverifikasi_oleh` (FK nullable) |
| `create_iuran_bumdes_table` | `id_iuran` (PK), `id_bumdes` (FK), `bulan_tahun`, `jumlah` default 50000, `status` (enum 2 nilai), `sumber_dana` (enum nullable) |
| `create_kas_bumdes_table` | `id_kas` (PK), `id_bumdes` (FK unique — relasi 1:1), `saldo` decimal default 0 |
| `create_kas_mutasi_table` | `id_mutasi` (bigIncrements), `id_kas` (FK), `tipe` (enum masuk/keluar), `sumber` (enum referral/iuran/lainnya) |
| `create_referral_table` | `id_referral` (PK), `id_bumdes_pengaju` (FK), `id_bumdes_penerima` (FK nullable), `kode_unik` (unique), `status` (enum 6 nilai — lihat SRS 3.9) |
| `create_feedback_table` | `id_feedback` (PK), `dari_id_akun` (FK), `ke_id_bumdes` (FK), `ke_id_unit` (FK nullable), `status_tindak_lanjut` (enum 3 nilai, default belum) |

Jalankan migrasi setelah semua file selesai diisi:

```powershell
php artisan migrate
```

**Verifikasi Fase 9:** `php artisan migrate:status` menampilkan 13 migration baru berstatus `Ran`, cek juga struktur tabel via HeidiSQL sesuai kolom di atas.

**Commit setelah Fase 9:**
```
feat(database): tambah migration 13 entitas inti sesuai SRS Bab 3

Menambahkan struktur tabel: region, bumdes, unit_usaha, akun, pelanggan,
transaksi, tagihan, iuran_bumdes, kas_bumdes, kas_mutasi, referral, feedback.
Primary key memakai format ID prefix sesuai architecture.md Bab 9.
```

---

## FASE 10 — Eloquent Model & Relasi

```powershell
php artisan make:model Region
php artisan make:model Bumdes
php artisan make:model UnitUsaha
php artisan make:model Akun
php artisan make:model Pelanggan
php artisan make:model Transaksi
php artisan make:model Tagihan
php artisan make:model IuranBumdes
php artisan make:model KasBumdes
php artisan make:model KasMutasi
php artisan make:model Referral
php artisan make:model Feedback
```

### 10.1 Contoh isi Model dengan primary key custom (WAJIB di semua model karena PK bukan `id` auto-increment)

**`app/Models/Bumdes.php`**
```php
class Bumdes extends Model
{
    protected $table = 'bumdes';
    protected $primaryKey = 'id_bumdes';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_bumdes', 'id_kelurahan', 'nama_bumdes', 'status_aktif', 'tanggal_berdiri'];

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'id_kelurahan', 'id_region');
    }

    public function units(): HasMany
    {
        return $this->hasMany(UnitUsaha::class, 'id_bumdes', 'id_bumdes');
    }

    public function kas(): HasOne
    {
        return $this->hasOne(KasBumdes::class, 'id_bumdes', 'id_bumdes');
    }
}
```

Terapkan pola yang sama (`$incrementing = false`, `$keyType = 'string'`, `$primaryKey` sesuai nama kolom) ke **seluruh 12 model** kecuali `KasMutasi` (pakai `id_mutasi` bigIncrements biasa).

Tambahkan relasi Eloquent sesuai `database-schema.dbml` untuk tiap model (misal `UnitUsaha` punya `belongsTo(Bumdes::class)` dan `hasMany(Transaksi::class)`, dst).

**Verifikasi Fase 10:** buka Tinker, coba `Bumdes::first()` tidak error (meski masih null karena data belum ada).

```powershell
php artisan tinker
>>> App\Models\Bumdes::count()
```

**Commit setelah Fase 10:**
```
feat(models): tambah Eloquent model + relasi 12 entitas

Model memakai primary key string custom (bukan auto-increment) sesuai
skema ID prefix. Relasi antar model mengikuti database-schema.dbml.
```

---

## FASE 11 — Seeder Data Awal

```powershell
php artisan make:seeder RegionSeeder
php artisan make:seeder RoleSeeder
```

### 11.1 `RegionSeeder` — data wilayah Kecamatan Minggir

```php
public function run(): void
{
    Region::create(['id_region' => 'REG-3404', 'level' => 'provinsi', 'nama' => 'DI Yogyakarta']);
    Region::create(['id_region' => 'REG-3404-04', 'level' => 'kota', 'parent_id' => 'REG-3404', 'nama' => 'Kabupaten Sleman']);
    Region::create(['id_region' => 'REG-3404-04-070', 'level' => 'kecamatan', 'parent_id' => 'REG-3404-04', 'nama' => 'Minggir']);

    $kelurahan = [
        ['id_region' => 'REG-3404-04-070-001', 'nama' => 'Sendangagung', 'is_koordinator' => false],
        ['id_region' => 'REG-3404-04-070-002', 'nama' => 'Sendangarum', 'is_koordinator' => false],
        ['id_region' => 'REG-3404-04-070-003', 'nama' => 'Sendangmulyo', 'is_koordinator' => false],
        ['id_region' => 'REG-3404-04-070-004', 'nama' => 'Sendangrejo', 'is_koordinator' => false],
        ['id_region' => 'REG-3404-04-070-005', 'nama' => 'Sendangsari', 'is_koordinator' => true],
    ];

    foreach ($kelurahan as $k) {
        Region::create([
            'id_region' => $k['id_region'],
            'level' => 'kelurahan',
            'parent_id' => 'REG-3404-04-070',
            'nama' => $k['nama'],
            'is_koordinator' => $k['is_koordinator'],
        ]);
    }
}
```

### 11.2 `RoleSeeder` — 9 role sesuai SRS Bab 3.10

```php
use Spatie\Permission\Models\Role;

public function run(): void
{
    $roles = [
        'super_admin', 'pengawas', 'penasihat', 'direktur',
        'admin_bumdes', 'sekretaris', 'bendahara', 'admin_unit', 'pengguna',
    ];

    foreach ($roles as $role) {
        Role::firstOrCreate(['name' => $role]);
    }
}
```

### 11.3 Daftarkan seeder di `DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call([
        RegionSeeder::class,
        RoleSeeder::class,
    ]);
}
```

Jalankan:

```powershell
php artisan migrate:fresh --seed
```

**Verifikasi Fase 11:** cek tabel `region` berisi 3 level (provinsi, kota, kecamatan) + 5 kelurahan, dengan Sendangsari `is_koordinator = 1`. Cek tabel `roles` berisi 9 baris.

**Commit setelah Fase 11:**
```
feat(seeder): tambah seeder wilayah Kec. Minggir dan 9 role sistem

RegionSeeder mengisi struktur provinsi-kota-kecamatan-kelurahan untuk
5 kelurahan Kec. Minggir, Sendangsari ditandai sebagai koordinator.
RoleSeeder mendaftarkan 9 role sesuai SRS Bab 3.10.
```

---

## FASE 12 — RBAC: Policy per Model

```powershell
php artisan make:policy BumdesPolicy --model=Bumdes
php artisan make:policy UnitUsahaPolicy --model=UnitUsaha
php artisan make:policy TransaksiPolicy --model=Transaksi
php artisan make:policy TagihanPolicy --model=Tagihan
php artisan make:policy ReferralPolicy --model=Referral
```

Contoh isi `UnitUsahaPolicy` (pola yang sama dipakai di seluruh policy — cek `architecture.md` Bab 4):

```php
public function update(Akun $akun, UnitUsaha $unit): bool
{
    if ($akun->hasRole('super_admin')) return true;
    if ($akun->hasRole('admin_bumdes') && $akun->id_bumdes === $unit->id_bumdes) return true;
    return false;
}

public function toggleStatus(Akun $akun, UnitUsaha $unit): bool
{
    return $akun->hasRole('admin_bumdes') && $akun->id_bumdes === $unit->id_bumdes;
}
```

Daftarkan tiap policy di `app/Providers/AuthServiceProvider.php` (`$policies` array).

**Verifikasi Fase 12:** `php artisan tinker` → `Gate::forUser($akun)->allows('update', $unit)` mengembalikan `true`/`false` sesuai role.

**Commit setelah Fase 12:**
```
feat(rbac): tambah policy akses 5 model utama

Policy mengecek kombinasi role + kepemilikan data (id_bumdes/id_unit)
sesuai matriks akses BRD Bab 11, bukan hanya permission generik.
```

---

## FASE 13 — Filament Resource (Urutan sesuai Prioritas PRD Bab 5)

```powershell
php artisan make:filament-resource Region --generate
php artisan make:filament-resource Bumdes --generate
php artisan make:filament-resource UnitUsaha --generate
php artisan make:filament-resource Akun --generate
php artisan make:filament-resource Pelanggan --generate
php artisan make:filament-resource Transaksi --generate
php artisan make:filament-resource Tagihan --generate
php artisan make:filament-resource IuranBumdes --generate
php artisan make:filament-resource Referral --generate
php artisan make:filament-resource Feedback --generate
```

Flag `--generate` membuat form & table columns otomatis berdasarkan struktur database — tetap perlu disesuaikan manual untuk: enum jadi `Select`, kolom `status_aktif` jadi `Toggle`, tombol aksi khusus (toggle status, verifikasi, redeem referral) ditambahkan sebagai `Action` custom di Resource masing-masing.

Setelah Resource dasar jalan, pisahkan ke 3 panel sesuai `architecture.md` Bab 5 (`SuperAdmin`, `Bumdes`, `Monitoring`) dengan memindahkan namespace dan mengatur `canViewAny()` per Resource sesuai role.

**Verifikasi Fase 13:** buka `http://sim-bumdes.test/admin`, seluruh resource muncul di sidebar dan CRUD dasar (create/edit/delete) berjalan tanpa error.

**Commit setelah Fase 13:**
```
feat(filament): tambah Resource CRUD untuk 10 modul utama

Generate awal dari struktur database, form/table disesuaikan manual
untuk enum (Select), status aktif (Toggle), dan action khusus per modul.
```

---

## FASE 14 — Testing Dasar

```powershell
php artisan make:test BumdesTest
php artisan make:test ReferralTest
```

Fokus pengujian awal: aturan bisnis kritis dari `SRS.md` (state machine referral, verifikasi tagihan, toggle status berjenjang) — bukan sekadar CRUD dasar yang sudah tercakup Filament.

```powershell
php artisan test
```

**Verifikasi Fase 14:** seluruh test yang dibuat berstatus `PASS`.

**Commit setelah Fase 14:**
```
test: tambah unit test state machine referral dan verifikasi tagihan
```

---

## Ringkasan Commit per Fase (Rekap)

| Fase | Pesan Commit |
|---|---|
| 9 — Migration | `feat(database): tambah migration 13 entitas inti sesuai SRS Bab 3` |
| 10 — Model | `feat(models): tambah Eloquent model + relasi 12 entitas` |
| 11 — Seeder | `feat(seeder): tambah seeder wilayah Kec. Minggir dan 9 role sistem` |
| 12 — Policy/RBAC | `feat(rbac): tambah policy akses 5 model utama` |
| 13 — Filament Resource | `feat(filament): tambah Resource CRUD untuk 10 modul utama` |
| 14 — Testing | `test: tambah unit test state machine referral dan verifikasi tagihan` |

---

## Langkah Berikutnya Setelah Fase 14

1. Implementasi Service Layer (`app/Services/`) untuk logika bisnis kompleks: `ReferralService` (state machine), `IuranService` (generate bulanan), `TagihanService` (verifikasi).
2. Buat Scheduled Command: `referral:expire-check`, `referral:verify-check`, `iuran:generate-bulanan` (lihat `architecture.md` Bab 6).
3. Bangun Portal Pengguna (Livewire) untuk cek tagihan & upload bukti transfer.
4. Implementasi modul Feedback + notifikasi real-time.
5. Implementasi ekspor laporan Excel per unit/BUMDes.
