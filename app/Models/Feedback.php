<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $primaryKey = 'id_feedback';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_feedback',
        'dari_id_akun',
        'ke_id_bumdes',
        'ke_id_unit',
        'isi_catatan',
        'status_tindak_lanjut',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    public function dariAkun(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'dari_id_akun', 'id_akun');
    }

    public function bumdes(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'ke_id_bumdes', 'id_bumdes');
    }

    public function unitUsaha(): BelongsTo
    {
        return $this->belongsTo(UnitUsaha::class, 'ke_id_unit', 'id_unit');
    }
}
