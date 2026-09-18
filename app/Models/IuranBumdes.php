<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuranBumdes extends Model
{
    protected $table = 'iuran_bumdes';

    protected $primaryKey = 'id_iuran';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_iuran',
        'id_bumdes',
        'bulan_tahun',
        'jumlah',
        'status',
        'sumber_dana',
        'tanggal_bayar',
        'diverifikasi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function bumdes(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function diverifikator(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'diverifikasi_oleh', 'id_akun');
    }
}
