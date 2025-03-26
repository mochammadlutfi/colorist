<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colorant extends Model
{
    protected $table = 'colorants';

    protected $fillable = [
        'code', 'name', 'rgb'
    ];
}
