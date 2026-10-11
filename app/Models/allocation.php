<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class allocation extends Model
{
    protected $table = 'allocation';

    protected $fillable = [
        'invoiceno',
        'paymentno',
        'amount',
        'tdate',
        'businessid',
        'createdby'
    ];
}
