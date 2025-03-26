<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
class StockQuantRequest extends FormRequest
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
            'location_id' => 'required',
            'qty' => 'required|numeric',
        ];
    }

    

    public function withValidator(Validator $validator)
    {

        $validator->sometimes('variant_id', 'required', function ($input) { 
            return $input->type === 'variant'; 
        });
        
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'location_id' => __('stock.destination_location'),
            'qty' => __('stock.quantity'),
            'variant_id' => __('product.variant'),
        ];
    }
}
