<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasBumdes extends Model
{
    protected $table = 'kas_bumdes';

    protected $primaryKey = 'id_kas';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_kas',
        'id_bumdes',
        'saldo',
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'decimal:2',
        ];
    }

    public function bumdes(): BelongsTo
    {
        return $this->belongsTo(Bumdes::class, 'id_bumdes', 'id_bumdes');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(KasMutasi::class, 'id_kas', 'id_kas');
    }
}
