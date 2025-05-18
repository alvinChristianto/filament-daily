<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryCustomer extends Model
{
    public function laundryTransaction(): HasMany
    {
        return $this->hasMany(LaundryTransaction::class, 'id');
    }
}
