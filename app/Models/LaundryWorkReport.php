<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryWorkReport extends Model
{
    // use HasFactory;

    // protected $table = 'laundry_work_reports';
    // protected $primaryKey = 'id_report';
    // public $incrementing = false;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [
        'transaction_detail' => 'json',

    ];
    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    // protected $keyType = 'string';

    public function laundryTransaction(): BelongsTo
    {
        return $this->belongsTo(LaundryTransaction::class, 'id_transaction');
    }

    // public function worker(): BelongsTo
    // {
    //     return $this->belongsTo(LaundryWorker::class, 'id_worker');
    // }
}
