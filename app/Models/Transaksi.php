<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaksi extends Model
{
    use LogsActivity;

    protected $table = 'transaksi';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->setDescriptionForEvent(fn (string $eventName) => "Transaksi {$eventName}");
    }

    protected $primaryKey = 'id_transaksi';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'id_unit',
        'tipe',
        'jumlah',
        'detail',
        'tanggal',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'detail' => 'array',
            'tanggal' => 'date',
        ];
    }

    public function unitUsaha(): BelongsTo
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit', 'id_unit');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'dicatat_oleh', 'id_akun');
    }
}
