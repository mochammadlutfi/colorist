<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasePaint extends Model
{
    protected $table = 'base_paints';

    protected $fillable = [
        'code', 'name', 'product_id', 'size', 'unit', 'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
    
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
