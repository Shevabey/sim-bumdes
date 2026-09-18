<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasMutasi extends Model
{
    protected $table = 'kas_mutasi';

    protected $primaryKey = 'id_mutasi';

    public const UPDATED_AT = null;

    protected $fillable = [
        'id_kas',
        'tipe',
        'jumlah',
        'sumber',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal' => 'datetime',
        ];
    }

    public function kas(): BelongsTo
    {
        return $this->belongsTo(KasBumdes::class, 'id_kas', 'id_kas');
    }
}
