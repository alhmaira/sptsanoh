<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $table = 'delivery';

    protected $fillable = [
        'docNumber',
        'supplierSearch',
        'createdOn',
        'del_month',
        'del_year',
        'otd',
        'qty_ord',
        'qty_rec',
        'fulfillment',
        'del_method',
        'premium',
        'dps',
        'problems',
        'has_problem',
        'total_score',
        'updatedBy',
        'createdBy',
    ];
}