<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Database\Eloquent\Model;

class MyinvoisMsicCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category',
    ];
}
