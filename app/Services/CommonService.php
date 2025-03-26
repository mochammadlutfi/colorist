<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\Product\Unit;
class CommonService
{

    public static function unitConversion($from_unit_id, $baseUnit)
    {
        $orderedUnit = Unit::find($from_unit_id);
        $baseUnit = Unit::find($baseUnit);

        if ($orderedUnit->id === $baseUnit->id) {
            return 1;
        }
        
        if ($orderedUnit->base_unit_id === $baseUnit->id) {
            return $orderedUnit->operator === '*' 
                ? $orderedUnit->operator_value 
                : 1 / $orderedUnit->operator_value;
        }

        throw new \Exception('Invalid unit conversion setup.');
    }

}