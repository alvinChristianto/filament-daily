<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRevenueExpenses extends Model
{

    // $CATEGORY_OF_REVENUE_AND_EXPENSES = 
    // ['PEND_SPAREPART', 'PEND_SERVICE_AC', 'PEND_LAUNDRY', 
    // 'BIAYA_BELI_SPAREPART', 'BIAYA_PEMBIAYAAN'];

    use HasFactory;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [];
}
