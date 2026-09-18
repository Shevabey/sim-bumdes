<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Akun extends Model
{
    protected $table = 'akun';

    protected $primaryKey = 'id_akun';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_akun',
        'nama',
        'username',
        'password_hash',
        'role',
        'id_bumdes',
        'id_unit',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
        ];
    }

    public function bumdes(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function unitUsaha(): BelongsTo
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit', 'id_unit');
    }

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class, 'id_akun', 'id_akun');
    }

    public function transaksiDicatat(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'dicatat_oleh', 'id_akun');
    }

    public function tagihanDiverifikasi(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'diverifikasi_oleh', 'id_akun');
    }

    public function iuranDiverifikasi(): HasMany
    {
        return $this->hasMany(IuranBumdes::class, 'diverifikasi_oleh', 'id_akun');
    }

    public function feedbackDikirim(): HasMany
    {
        return $this->hasMany(Feedback::class, 'dari_id_akun', 'id_akun');
    }
}
