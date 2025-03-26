<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchColorant extends Model
{
    protected $table = 'batch_colorants';

    protected $fillable = [
        'batch_id', 'colorant_id', 'used_amount', 'final_used_amount', 'sequence_number', 'price'
    ];

    
    protected $casts = [
        'price' => 'decimal:2',
        'used_amount' => 'decimal:2', // Cast to decimal with 2 decimal places
        'final_used_amount' => 'decimal:2', // Cast to decimal with 2 decimal places
    ];

    
    public function colorant(){
        return $this->belongsTo(Colorant::class, 'colorant_id');
    }
}
