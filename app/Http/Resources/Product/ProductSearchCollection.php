<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSearchCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->product->name,
            'var1' => $this->var1,
            'var2' => $this->var2,
            'name' => $this->name,
            'unit' => $this->product->unit,
            'unit_id' => $this->product->unit_id,
            'base_unit_id' => $this->product->base_unit_id,
            'price' => $this->price,
            'sku' => $this->sku,
            'type' => $this->product->type,
            'stock' => $this->stock_quant()->where('location_id', 6)->sum('qty'),
            'stock_control' => $this->product->stock_control,
            'cost' => $this->cost,
        ];
    }
}
