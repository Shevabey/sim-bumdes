<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bumdes extends Model
{
    protected $table = 'bumdes';

    protected $primaryKey = 'id_bumdes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_bumdes',
        'id_kelurahan',
        'nama_bumdes',
        'status_aktif',
        'tanggal_berdiri',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'tanggal_berdiri' => 'date',
        ];
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'id_kelurahan', 'id_region');
    }

    public function unitUsaha(): HasMany
    {
        return $this->hasMany(UnitUsaha::class, 'id_bumdes', 'id_bumdes');
    }

    public function units(): HasMany
    {
        return $this->unitUsaha();
    }

    public function akun(): HasMany
    {
        return $this->hasMany(Akun::class, 'id_bumdes', 'id_bumdes');
    }

    public function kas(): HasOne
    {
        return $this->hasOne(KasBumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function iuran(): HasMany
    {
        return $this->hasMany(IuranBumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function referralDiajukan(): HasMany
    {
        return $this->hasMany(Referral::class, 'id_bumdes_pengaju', 'id_bumdes');
    }

    public function referralDiterima(): HasMany
    {
        return $this->hasMany(Referral::class, 'id_bumdes_penerima', 'id_bumdes');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'ke_id_bumdes', 'id_bumdes');
    }
}
