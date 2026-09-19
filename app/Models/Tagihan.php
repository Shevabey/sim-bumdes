<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tagihan extends Model
{
    use LogsActivity;

    protected $table = 'tagihan';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'metode', 'bukti_transfer_url', 'diverifikasi_oleh', 'tanggal_verifikasi'])
            ->setDescriptionForEvent(fn (string $eventName) => "Tagihan {$eventName}");
    }

    protected $primaryKey = 'id_tagihan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_tagihan',
        'id_pelanggan',
        'id_unit',
        'jumlah',
        'jatuh_tempo',
        'status',
        'metode',
        'bukti_transfer_url',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function unitUsaha(): BelongsTo
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit', 'id_unit');
    }

    public function diverifikator(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'diverifikasi_oleh', 'id_akun');
    }
}
