<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expenses extends Model
{
    use HasFactory;

    protected $table = 'expenses';
    protected $primaryKey = 'id_expenses';
    public $incrementing = false;

    // protected $casts = ['id_hotel' => 'string'];
    protected $casts = [
        'image_expenses' => 'array'
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
}
