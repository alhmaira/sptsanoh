<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QC extends Model
{
    protected $table = 'qc';

    protected $fillable = [

    'docNumber',
    'supplier',
    'del_month',
    'del_year',

    'lineStop',
    'ng',
    'supply',

    'ppm',
    'ppmScore',

    'rank_score',
    'fppk',

    'qualityProblems',
    'has_problem',

    'total_score',

    'created_by',
    'updated_by'

];
}