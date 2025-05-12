<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcCustomer extends Model
{
    public function acworkingReport(): HasMany
    {
        return $this->hasMany(AcWorkingReport::class);
    }
}
