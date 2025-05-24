<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparepartShipment extends Model
{
    use HasFactory;

    protected $table = 'sparepart_transaction_shipments';
    protected $primaryKey = 'id_transaction';
    public $incrementing = false;

    protected $casts = [
        'transaction_detail' => 'json',
        'payment_image' => 'array'
    ];
    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    // public function sparepart(): BelongsTo
    // {
    //     return $this->belongsTo(Sparepart::class, 'id_sparepart');
    // }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'id_warehouse');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_payment');
    }
}
