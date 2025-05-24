<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sparepart extends Model
{
    // public function sparepartShipment(): HasMany
    // {
    //     return $this->hasMany(SparepartShipment::class, 'id_sparepart');
    // }
    
    public function sparepartStock(): HasMany
    {
        return $this->hasMany(SparepartStock::class, 'id_sparepart');
    }
}
