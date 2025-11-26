<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparepartExpense extends Model
{

    protected $casts = [
        'expense_sparepart_detail' => 'json',
        'expenses_payment_detail' => 'json',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_category');
    }
}
