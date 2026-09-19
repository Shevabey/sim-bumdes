<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Referral extends Model
{
    use LogsActivity;

    protected $table = 'referral';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'id_bumdes_penerima', 'tanggal_redeem', 'batas_verifikasi', 'tanggal_cair'])
            ->setDescriptionForEvent(fn (string $eventName) => "Referral {$eventName}");
    }

    protected $primaryKey = 'id_referral';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_referral',
        'id_bumdes_pengaju',
        'id_bumdes_penerima',
        'kode_unik',
        'tanggal_generate',
        'tanggal_expired',
        'status',
        'tanggal_redeem',
        'batas_verifikasi',
        'tanggal_cair',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_generate' => 'datetime',
            'tanggal_expired' => 'datetime',
            'tanggal_redeem' => 'datetime',
            'batas_verifikasi' => 'datetime',
            'tanggal_cair' => 'datetime',
        ];
    }

    public function bumdesPengaju(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes_pengaju', 'id_bumdes');
    }

    public function bumdesPenerima(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes_penerima', 'id_bumdes');
    }
}
