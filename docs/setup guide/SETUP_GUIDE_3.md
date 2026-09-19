# Panduan Instalasi Lanjutan — SIM-BUMDes
## Fase 15-20: Service Layer, Scheduled Command, Portal Pengguna, Feedback, Ekspor Excel

**Prasyarat:** `SETUP_GUIDE_LANJUTAN.md` Fase 9-14 sudah selesai (migration, model, seeder, policy, Filament Resource, testing dasar sudah jalan).
**Akses aplikasi di panduan ini:** `http://localhost:8000/` — jalankan server dengan:

```powershell
php artisan serve
```

Biarkan terminal ini tetap berjalan selama development pada panduan ini (buka terminal baru untuk menjalankan perintah `artisan` lainnya).

**Commit message pada panduan ini mengikuti [Conventional Commits](https://www.conventionalcommits.org/):**
```
<type>(<scope>): <deskripsi singkat>

<body opsional — jelaskan detail perubahan>
```
Tipe yang dipakai: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`.

---

## FASE 15 — Service Layer (Business Logic)

Business logic kompleks dipisah dari Controller/Livewire/Filament agar dapat dipakai ulang dan mudah diuji. Buat folder & file berikut:

```powershell
mkdir app\Services
```

### 15.1 `app/Services/ReferralService.php`

Menangani seluruh state machine referral sesuai `SRS.md` Bab 3.9.

```php
namespace App\Services;

use App\Models\Bumdes;
use App\Models\Referral;
use App\Models\KasBumdes;
use App\Models\KasMutasi;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReferralService
{
    public function generateKode(Bumdes $pengaju): Referral
    {
        // Nonaktifkan kode aktif lama milik BUMDes ini (jika ada)
        Referral::where('id_bumdes_pengaju', $pengaju->id_bumdes)
            ->where('status', 'aktif')
            ->update(['status' => 'kedaluwarsa']);

        $kode = strtoupper(Str::random(6));

        return Referral::create([
            'id_referral' => "REF-{$pengaju->id_bumdes}-{$kode}",
            'id_bumdes_pengaju' => $pengaju->id_bumdes,
            'kode_unik' => $kode,
            'tanggal_generate' => now(),
            'tanggal_expired' => now()->addDays(5),
            'status' => 'aktif',
        ]);
    }

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
            'status' => 'pending',
            'tanggal_redeem' => now(),
            'batas_verifikasi' => now()->addDays(15),
        ]);

        // Otomatis generate kode baru untuk BUMDes pengaju
        $this->generateKode($referral->bumdesPengaju);

        return $referral;
    }

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

    public function cairkan(Referral $referral): void
    {
        $kas = KasBumdes::firstOrCreate(
            ['id_bumdes' => $referral->id_bumdes_pengaju],
            ['id_kas' => "KAS-{$referral->id_bumdes_pengaju}", 'saldo' => 0]
        );

        $kas->increment('saldo', 10000);

        KasMutasi::create([
            'id_kas' => $kas->id_kas,
            'tipe' => 'masuk',
            'jumlah' => 10000,
            'sumber' => 'referral',
            'keterangan' => "Pencairan referral {$referral->id_referral}",
            'tanggal' => now(),
        ]);

        $referral->update(['status' => 'cair', 'tanggal_cair' => now()]);
    }
}
```

### 15.2 `app/Services/IuranService.php`

```php
namespace App\Services;

use App\Models\Bumdes;
use App\Models\IuranBumdes;
use App\Models\KasBumdes;
use App\Models\KasMutasi;

class IuranService
{
    public function generateBulanan(): int
    {
        $bulanIni = now()->format('Y-m');
        $jumlahDibuat = 0;

        Bumdes::where('status_aktif', true)->each(function (Bumdes $bumdes) use ($bulanIni, &$jumlahDibuat) {
            $sudahAda = IuranBumdes::where('id_bumdes', $bumdes->id_bumdes)
                ->where('bulan_tahun', $bulanIni)
                ->exists();

            if (! $sudahAda) {
                IuranBumdes::create([
                    'id_iuran' => "IUR-{$bumdes->id_bumdes}-{$bulanIni}",
                    'id_bumdes' => $bumdes->id_bumdes,
                    'bulan_tahun' => $bulanIni,
                    'jumlah' => 50000,
                    'status' => 'belum_bayar',
                ]);
                $jumlahDibuat++;
            }
        });

        return $jumlahDibuat;
    }

    public function bayarDariKas(IuranBumdes $iuran): void
    {
        $kas = KasBumdes::where('id_bumdes', $iuran->id_bumdes)->firstOrFail();

        abort_if($kas->saldo < $iuran->jumlah, 422, 'Saldo kas tidak mencukupi.');

        $kas->decrement('saldo', $iuran->jumlah);

        KasMutasi::create([
            'id_kas' => $kas->id_kas,
            'tipe' => 'keluar',
            'jumlah' => $iuran->jumlah,
            'sumber' => 'iuran',
            'keterangan' => "Bayar iuran {$iuran->bulan_tahun} dari kas",
            'tanggal' => now(),
        ]);

        $iuran->update(['status' => 'lunas', 'sumber_dana' => 'kas', 'tanggal_bayar' => now()]);
    }
}
```

### 15.3 `app/Services/TagihanService.php`

```php
namespace App\Services;

use App\Models\Tagihan;
use App\Models\Akun;

class TagihanService
{
    public function verifikasi(Tagihan $tagihan, Akun $verifikator, bool $approve, ?string $catatan = null): Tagihan
    {
        $tagihan->update([
            'status' => $approve ? 'lunas' : 'ditolak',
            'diverifikasi_oleh' => $verifikator->id_akun,
            'tanggal_verifikasi' => now(),
        ]);

        return $tagihan;
    }
}
```

**Verifikasi Fase 15:** jalankan lewat Tinker:
```powershell
php artisan tinker
>>> app(App\Services\IuranService::class)->generateBulanan()
```
Harus mengembalikan angka (jumlah BUMDes aktif yang berhasil dibuatkan tagihan iuran), tanpa error.

**Commit setelah Fase 15:**
```
feat(services): tambah service layer untuk referral, iuran, dan tagihan

Implementasi state machine referral (generate, redeem, cek aktivitas,
pencairan kas) sesuai SRS 3.9, generate iuran bulanan otomatis (SRS 3.7),
dan verifikasi tagihan (SRS 3.6).
```

---

## FASE 16 — Scheduled Command

Buat command untuk menjalankan Service secara terjadwal.

```powershell
php artisan make:command GenerateIuranBulanan
php artisan make:command ReferralExpireCheck
php artisan make:command ReferralVerifyCheck
```

### 16.1 `app/Console/Commands/GenerateIuranBulanan.php`

```php
protected $signature = 'iuran:generate-bulanan';
protected $description = 'Generate tagihan iuran bulanan Rp50.000 untuk seluruh BUMDes aktif';

public function handle(IuranService $service): int
{
    $jumlah = $service->generateBulanan();
    $this->info("Berhasil membuat {$jumlah} tagihan iuran baru.");
    return self::SUCCESS;
}
```

### 16.2 `app/Console/Commands/ReferralExpireCheck.php`

```php
protected $signature = 'referral:expire-check';
protected $description = 'Cek dan tandai kode referral yang sudah lewat 5 hari sebagai kedaluwarsa';

public function handle(): int
{
    $expired = Referral::where('status', 'aktif')
        ->where('tanggal_expired', '<', now())
        ->get();

    foreach ($expired as $referral) {
        $referral->update(['status' => 'kedaluwarsa']);
        app(ReferralService::class)->generateKode($referral->bumdesPengaju);
    }

    $this->info("{$expired->count()} kode referral kedaluwarsa diproses.");
    return self::SUCCESS;
}
```

### 16.3 `app/Console/Commands/ReferralVerifyCheck.php`

```php
protected $signature = 'referral:verify-check';
protected $description = 'Cek referral pending, cairkan jika syarat aktivitas terpenuhi dalam 15 hari, atau tandai gagal';

public function handle(ReferralService $service): int
{
    $pendingList = Referral::where('status', 'pending')->get();

    foreach ($pendingList as $referral) {
        if ($service->cekAktivitasMinimal($referral)) {
            $service->cairkan($referral);
        } elseif (now()->greaterThan($referral->batas_verifikasi)) {
            $referral->update(['status' => 'gagal']);
        }
    }

    $this->info("{$pendingList->count()} referral pending diproses.");
    return self::SUCCESS;
}
```

### 16.4 Daftarkan jadwal di `routes/console.php` (Laravel 11)

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('iuran:generate-bulanan')->monthlyOn(1, '00:05');
Schedule::command('referral:expire-check')->hourly();
Schedule::command('referral:verify-check')->daily();
```

**Verifikasi Fase 16:** jalankan manual tiap command satu-persatu, pastikan tidak error:
```powershell
php artisan iuran:generate-bulanan
php artisan referral:expire-check
php artisan referral:verify-check
```
Lalu cek scheduler terdaftar: `php artisan schedule:list`.

**Commit setelah Fase 16:**
```
feat(console): tambah scheduled command iuran dan referral

Menambahkan iuran:generate-bulanan (tiap tanggal 1), referral:expire-check
(tiap jam), dan referral:verify-check (harian), didaftarkan di
routes/console.php sesuai architecture.md Bab 6.
```

---

## FASE 17 — Portal Pengguna (Livewire)

Portal ini terpisah dari Filament, dipakai role `pengguna` (pelanggan) untuk cek tagihan dan upload bukti transfer.

```powershell
php artisan make:livewire Portal/CekTagihan
php artisan make:livewire Portal/UploadBukti
```

### 17.1 `app/Http/Livewire/Portal/CekTagihan.php` (ringkas)

```php
namespace App\Http\Livewire\Portal;

use Livewire\Component;
use App\Models\Tagihan;

class CekTagihan extends Component
{
    public function render()
    {
        $tagihan = Tagihan::where('id_pelanggan', auth()->user()->pelanggan->id_pelanggan)
            ->orderByDesc('jatuh_tempo')
            ->get();

        return view('livewire.portal.cek-tagihan', compact('tagihan'));
    }
}
```

### 17.2 `app/Http/Livewire/Portal/UploadBukti.php` (ringkas, dengan `wire:loading` untuk toleransi koneksi lambat)

```php
namespace App\Http\Livewire\Portal;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tagihan;

class UploadBukti extends Component
{
    use WithFileUploads;

    public Tagihan $tagihan;
    public $bukti;

    protected $rules = ['bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'];

    public function simpan()
    {
        $this->validate();

        $path = $this->bukti->store('bukti-transfer', 'public');

        $this->tagihan->update([
            'metode' => 'transfer',
            'bukti_transfer_url' => $path,
            'status' => 'menunggu_verifikasi',
        ]);

        session()->flash('message', 'Bukti transfer berhasil diunggah, menunggu verifikasi admin unit.');
    }

    public function render()
    {
        return view('livewire.portal.upload-bukti');
    }
}
```

Tambahkan `wire:loading` dan `wire:target="simpan"` pada tombol submit di file Blade view-nya agar pengguna mendapat umpan balik jelas saat koneksi lambat (sesuai NFR di `SRS.md` Bab 5).

### 17.3 Route portal (`routes/web.php`)

```php
use App\Http\Livewire\Portal\CekTagihan;
use App\Http\Livewire\Portal\UploadBukti;

Route::middleware(['auth', 'role:pengguna'])->prefix('portal')->group(function () {
    Route::get('/tagihan', CekTagihan::class)->name('portal.tagihan');
    Route::get('/tagihan/{tagihan}/upload', UploadBukti::class)->name('portal.upload-bukti');
});
```

**Verifikasi Fase 17:** login sebagai akun role `pengguna`, buka `http://localhost:8000/portal/tagihan` — daftar tagihan tampil; coba upload bukti transfer di salah satu tagihan berstatus `belum_bayar`, status berubah menjadi `menunggu_verifikasi`.

**Commit setelah Fase 17:**
```
feat(portal): tambah portal pengguna untuk cek tagihan dan upload bukti transfer

Komponen Livewire CekTagihan dan UploadBukti, dilengkapi validasi file
(max 2MB, jpg/png/pdf) dan wire:loading untuk toleransi koneksi lambat
sesuai SRS Bab 5.
```

---

## FASE 18 — Modul Feedback & Notifikasi Real-time

```powershell
php artisan make:notification FeedbackDiterima
```

### 18.1 `app/Notifications/FeedbackDiterima.php`

```php
namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class FeedbackDiterima extends Notification
{
    public function __construct(protected Feedback $feedback) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id_feedback' => $this->feedback->id_feedback,
            'isi_catatan' => $this->feedback->isi_catatan,
            'dari' => $this->feedback->pengirim->nama,
        ];
    }
}
```

### 18.2 Kirim notifikasi saat Feedback dibuat (contoh di FeedbackService atau observer)

```php
$akunTerkait = Akun::where('id_bumdes', $feedback->ke_id_bumdes)->get();
Notification::send($akunTerkait, new FeedbackDiterima($feedback));
```

### 18.3 Tampilkan notifikasi real-time sederhana dengan polling di Livewire

```php
// Komponen NotifikasiBadge.php
public function render()
{
    return view('livewire.notifikasi-badge', [
        'jumlahBelumDibaca' => auth()->user()->unreadNotifications->count(),
    ]);
}
```
```blade
{{-- resources/views/livewire/notifikasi-badge.blade.php --}}
<div wire:poll.10s>
    Notifikasi baru: {{ $jumlahBelumDibaca }}
</div>
```

**Verifikasi Fase 18:** buat Feedback baru dari akun `pengawas` ke sebuah BUMDes lewat panel Monitoring, cek tabel `notifications` terisi, dan badge notifikasi di akun `admin_bumdes` terkait bertambah dalam 10 detik (via polling).

**Commit setelah Fase 18:**
```
feat(feedback): tambah notifikasi real-time saat feedback diterima

Notifikasi database channel dikirim ke seluruh akun terkait BUMDes/unit,
ditampilkan di badge Livewire dengan wire:poll setiap 10 detik (FR-34).
```

---

## FASE 19 — Ekspor Laporan Excel

```powershell
php artisan make:export UnitTransaksiExport --model=Transaksi
php artisan make:export BumdesLaporanExport
```

### 19.1 `app/Exports/UnitTransaksiExport.php` (ringkas)

```php
namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnitTransaksiExport implements FromCollection, WithHeadings
{
    public function __construct(protected string $idUnit) {}

    public function collection()
    {
        return Transaksi::where('id_unit', $this->idUnit)
            ->orderBy('tanggal')
            ->get(['id_transaksi', 'tipe', 'jumlah', 'tanggal']);
    }

    public function headings(): array
    {
        return ['ID Transaksi', 'Tipe', 'Jumlah', 'Tanggal'];
    }
}
```

### 19.2 Route/Action pemicu unduhan (Filament Action atau Controller)

```php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnitTransaksiExport;

Route::get('/reports/unit/{id}/export', function (string $id) {
    return Excel::download(new UnitTransaksiExport($id), "laporan-unit-{$id}.xlsx");
})->middleware('auth')->name('reports.unit.export');
```

**Verifikasi Fase 19:** login sebagai `admin_unit`/`bendahara`, akses `http://localhost:8000/reports/unit/{id_unit}/export` — file `.xlsx` terunduh dan berisi data transaksi unit tersebut.

**Commit setelah Fase 19:**
```
feat(reports): tambah ekspor laporan transaksi unit ke Excel

Menggunakan maatwebsite/excel, endpoint /reports/unit/{id}/export
sesuai kontrak api-spec.json dan FR-28.
```

---

## FASE 20 — Integrasi Akhir & Checklist

| # | Cek | Cara Verifikasi | Status |
|---|---|---|---|
| 1 | Server berjalan di localhost | `http://localhost:8000/` tampil | ☐ |
| 2 | Referral end-to-end | Generate kode → redeem → jalankan `referral:verify-check` → status jadi `cair`/`gagal` sesuai simulasi | ☐ |
| 3 | Iuran otomatis | `iuran:generate-bulanan` membuat record untuk semua BUMDes aktif | ☐ |
| 4 | Portal pengguna | Login role `pengguna`, cek tagihan & upload bukti berjalan | ☐ |
| 5 | Notifikasi feedback | Badge notifikasi bertambah setelah feedback dikirim | ☐ |
| 6 | Ekspor Excel | File `.xlsx` terunduh dengan data benar | ☐ |
| 7 | Scheduler terdaftar | `php artisan schedule:list` menampilkan 3 command | ☐ |

**Commit setelah Fase 20 (jika ada penyesuaian akhir dari hasil testing integrasi):**
```
fix(integration): perbaikan hasil pengujian end-to-end fase percobaan
```

---

## Rekap Commit Fase 15-20

| Fase | Pesan Commit (Conventional Commits) |
|---|---|
| 15 — Service Layer | `feat(services): tambah service layer untuk referral, iuran, dan tagihan` |
| 16 — Scheduled Command | `feat(console): tambah scheduled command iuran dan referral` |
| 17 — Portal Pengguna | `feat(portal): tambah portal pengguna untuk cek tagihan dan upload bukti transfer` |
| 18 — Feedback & Notifikasi | `feat(feedback): tambah notifikasi real-time saat feedback diterima` |
| 19 — Ekspor Excel | `feat(reports): tambah ekspor laporan transaksi unit ke Excel` |
| 20 — Integrasi & Testing | `fix(integration): perbaikan hasil pengujian end-to-end fase percobaan` |

---

## Langkah Berikutnya Setelah Fase 20

1. Uji coba lapangan (UAT) bersama pengurus BUMDes Sendangsari & Sendangrejo sesuai metodologi riset dosen.
2. Kumpulkan feedback pengguna riil, catat sebagai revisi di `PRD.md`/`SRS.md` (jangan lupa update tabel Riwayat Revisi).
3. Setelah stabil, lanjut ke `SETUP_GUIDE.md` Fase 9 (Docker) untuk deployment ke VPS.
