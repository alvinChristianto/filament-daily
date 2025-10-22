<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class DailySparepartTrxRevenueExpenses extends Model
{
    use HasFactory;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_category');
    }

    public static function canView(): bool
    {
        // return auth()->user()->isSuperAdmin();
        return true;
    }
}
