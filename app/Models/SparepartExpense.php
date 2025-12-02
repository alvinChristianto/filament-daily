<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparepartExpense extends Model
{
    use HasFactory;

    protected $table = 'sparepart_expenses';
    protected $primaryKey = 'id_transaction';
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';


    protected $casts = [
        'expense_sparepart_detail' => 'json',
        'expense_payment_detail' => 'json',
    ];


    // public function payment(): BelongsTo
    // {
    //     return $this->belongsTo(Payment::class, 'payment_category');
    // }
}
