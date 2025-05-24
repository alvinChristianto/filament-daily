<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public function acworkingReport(): HasMany
    {
        return $this->hasMany(AcWorkingReport::class, 'id');
    }

    public function laundryTransaction(): HasMany
    {
        return $this->hasMany(LaundryTransaction::class, 'id');
    }

    public function sparepartTransactionShipment(): HasMany
    {
        return $this->hasMany(SparepartShipment::class, 'id');
    }
}
