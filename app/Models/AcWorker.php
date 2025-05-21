<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcWorker extends Model
{
      public function acWorkReport(): HasMany
    {
        return $this->hasMany(AcWorkingReport::class, 'id');
    }
}
