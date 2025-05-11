<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    public function sparepartShipment(): HasMany
    {
        return $this->hasMany(SparepartShipment::class);
    }

    public function sparepartStock(): HasMany
    {
        return $this->hasMany(SparepartStock::class);
    }
}
