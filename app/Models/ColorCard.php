<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorCard extends Model
{
    protected $table = 'color_cards';

    protected $fillable = [
        'code', 'name'
    ];
}
