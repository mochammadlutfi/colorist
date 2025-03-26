<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'partner_id' => 'required',
            'date' => 'required',
            'dest_location_id' => 'required',
            'lines' => 'required'
        ];
    }

    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'partner_id' => __('menu.vendor'),
            'date' => __('common.date'),
            'dest_location_id' => __('transaction.deliver_to'),
            'lines' => 'Order Lines'
        ];
    }
}
