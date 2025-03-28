<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
class MixingBatch extends Model
{
    protected $table = 'mixing_batches';

    protected $fillable = [
        'number', 'machine_code', 'color_mixing_time', 'batch_number', 'bucket_quantity', 'bucket_no', 'base_paint_id', 'product_id', 'color_card_id', 
        'series_id', 'filling_volume', 'unit_name', 'original_type', 'paint_type', 'formula_discount', 'formula_create_date', 
        'remark', 'barcode', 'result', 'result_message', 'outlet_id', 'profit', 'uploaded_by', 'price', 'base_price', 'type', 'upload_id'
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $outletCode = $model->outlet->machine_code;
            // dd($outletCode);

            $yearMonth = Carbon::parse($model->color_mixing_time)->format('Ym');
            
            $last = DB::table('mixing_batches')
                ->where('outlet_id', $model->outlet_id)
                ->whereRaw("DATE_FORMAT(color_mixing_time, '%Y%m') = ?", [$yearMonth])
                ->orderBy('number', 'desc')
                ->first();

            $lastNumber = $last ? (int) substr($last->number, -5) : 0;
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            
            $model->number = "TRX{$outletCode}/{$yearMonth}/{$newNumber}";
            // dd($model->number);
        });
    }
}
