<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MixingBatch extends Model
{
    protected $table = 'mixing_batches';

    protected $fillable = [
        'machine_code', 'color_mixing_time', 'batch_number', 'bucket_quantity', 'bucket_no', 'base_paint_id', 'product_id', 'color_card_id', 
        'series_id', 'filling_volume', 'unit_name', 'original_type', 'paint_type', 'formula_discount', 'formula_create_date', 
        'remark', 'barcode', 'result', 'result_message', 'outlet_id', 'profit', 'uploaded_by', 'price', 'base_price'
    ];

    public function outlet(){
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function series(){
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function color_card(){
        return $this->belongsTo(ColorCard::class, 'color_card_id');
    }

    public function base_paint(){
        return $this->belongsTo(BasePaint::class, 'base_paint_id');
    }
    
    public function lines(){
        return $this->hasMany(BatchColorant::class, 'batch_id');
    }
}
