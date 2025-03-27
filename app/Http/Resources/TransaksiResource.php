<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class TransaksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ari_no_tinting' => $this->batch_number,
            'ari_tgl_transaksi' => Carbon::parse($this->color_mixing_time)->format('Y-m-d'),
            'ari_jam_transaksi' => Carbon::parse($this->ari_jam_transaksi)->format('HH:mm:ss'),
            'ari_kode_cabang' => $this->outlet->machine_code,
            'ari_itemcode' => $this->product->code,
            'ari_itemname' => $this->product->name,
            'ari_base_price' => $this->base_price,
            'ari_qty_trx' => $this->bucket_quantity,
            'ari_price' => $this->price,
            'ari_kode_formula' => $this->color_card->code,
            'ari_nama_formula' => $this->color_card->name,
            'details' => $this->lines->map(function ($detail) {
                return [
                    'ari_kode_colorant' => $detail->colorant->code,
                    'ari_nama_colorant' => $detail->colorant->name,
                    'ari_amount_colorant' => $detail->final_used_amount,
                    'ari_price_colorant' => $detail->price,
                ];
            }),
        ];
    }
}
