<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcWorkingReport extends Model
{
    use HasFactory;

    protected $table = 'ac_working_reports';
    protected $primaryKey = 'id_report';
    public $incrementing = false;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [
        'transaction_detail' => 'json',
        'image_working' => 'array'
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
        return $this->belongsTo(AcCustomer::class, 'id_customer');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(AcWorker::class, 'id_worker');
    }

    public function acworkingComplain(): BelongsTo
    {
        return $this->belongsTo(AcWorkingComplain::class, 'id_report');
    }
    
}
