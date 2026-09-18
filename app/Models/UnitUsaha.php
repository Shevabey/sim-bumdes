<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitUsaha extends Model
{
    protected $table = 'unit_usaha';

    protected $primaryKey = 'id_unit';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_unit',
        'id_bumdes',
        'jenis_unit',
        'nama_unit',
        'skema_field',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'skema_field' => 'array',
            'status_aktif' => 'boolean',
        ];
    }

    public function bumdes(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function akunAdmin(): HasOne
    {
        return $this->hasOne(Akun::class, 'id_unit', 'id_unit');
    }

    public function pelanggan(): HasMany
    {
        return $this->hasMany(Pelanggan::class, 'id_unit', 'id_unit');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_unit', 'id_unit');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'id_unit', 'id_unit');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'ke_id_unit', 'id_unit');
    }
}
