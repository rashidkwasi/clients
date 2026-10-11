<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'clientid',
        'item',
        'description',
        'amount',
        'fees',
        'createdby',
        'created_at',
        'status',
        'businessid',
        'paymentno',
        'invoiceno',
        'tdate',
        'currency',
        'channel',
        'bank',
        'mobilenumber',
        'reference',
        'receiptno'

    ];
}







