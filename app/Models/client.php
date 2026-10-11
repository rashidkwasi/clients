<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'cleintid',
        'business_name',
        'business_owner',
        'email',
        'telephone',
        'address',
        'city',
        'url',
        'icon',
        'createdby',
        'created_at',
        'modifiedby',
        'modified_at',
        'plan',
        'status'
    ];
}
