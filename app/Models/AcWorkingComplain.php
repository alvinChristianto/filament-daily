<?php

namespace App\Models;

use App\Filament\Resources\AcWorkerResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcWorkingComplain extends Model
{
    protected $casts = [
        'image_complain' => 'array',
        'image_solving' => 'array'
    ];

    public function acworkingReport(): BelongsTo
    {
        return $this->belongsTo(AcWorkingReport::class, 'id_report');
    }
}
