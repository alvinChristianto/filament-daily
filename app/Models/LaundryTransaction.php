<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryTransaction extends Model
{

    use HasFactory;

    protected $table = 'laundry_transactions';
    protected $primaryKey = 'id_transaction';
    public $incrementing = false;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [
        'transaction_detail' => 'json',
    ];
    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';


    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_payment');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(LaundryCustomer::class, 'id_customer');
    }

    // public function packet(): BelongsTo
    // {
    //     return $this->belongsTo(LaundryPacket::class, 'id_packet');
    // }

}
