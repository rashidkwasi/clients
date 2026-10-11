<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoiceno',
        'period_start',
        'period_end',
        'month',
        'year',
        'totalamount',
        'paidamount',
        'duedate',
        'status',
        'businessid',
        'createdby',
        'created_at',
        'clientid'
    ];
}
