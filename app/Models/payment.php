<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'clientid',
        'item',
        'description',
        'amount',
        'createdby',
        'created_at',
        'status',
        'businessid',
        'paymentno',
        'invoiceno'
    ];
}
