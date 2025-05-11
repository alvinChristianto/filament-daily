<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparepartShipment extends Model
{
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'id_sparepart');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class,'id_warehouse');
    }
}
