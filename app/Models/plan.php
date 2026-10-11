<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'planid',
        'name',
        'setup_fee',
        'monthly_fee',
        'description',
        'status',
        'createdby',
        'created_at',
        'modifiedby',
        'modified_at'
    ];
}
