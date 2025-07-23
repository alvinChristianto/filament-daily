<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyRevenueExpenses extends Model
{

    // $CATEGORY_OF_REVENUE_AND_EXPENSES = 
    // ['PEND_SPAREPART', 'PEND_SERVICE_AC', 'PEND_LAUNDRY', 
    // 'BIAYA_BELI_SPAREPART', 'BIAYA_PEMBIAYAAN'];

    use HasFactory;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_category');
    }
}
