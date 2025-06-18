<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceReport extends Model
{
    protected $casts = [
        'is_present' => 'boolean',
    ];
}
