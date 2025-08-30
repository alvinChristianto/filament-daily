<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherRevenue extends Model
{
    use HasFactory;

    protected $table = 'other_revenues';
    protected $primaryKey = 'id_transaction';
    public $incrementing = false;
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

   
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_payment');
    }
}
