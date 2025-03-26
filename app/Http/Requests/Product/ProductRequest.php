<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
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
            'name' => 'required',
            'category_id' => 'required',
            'type' => 'required',
            'unit_id' => 'required',
        ];
    }

    

    public function withValidator(Validator $validator)
    {

        $validator->sometimes('var1_name', 'required', function ($input) { 
            return $input->type === 'variant'; 
        });
        
        $validator->sometimes('var1_val', 'required', function ($input) { 
            return $input->type === 'variant'; 
        });
        
        $validator->sometimes('var2_name', 'required', function ($input) { 
            return $input->type === 'variant' && $input->var2_enable == 'true'; 
        });
        
        $validator->sometimes('var2_val', 'required', function ($input) { 
            return $input->type === 'variant' && $input->var2_enable == 'true'; 
        });
        
        $validator->sometimes('variant', 'required', function ($input) { 
            return $input->type === 'variant' && count($input->var1_val); 
        });

        $validator->sometimes('variant.*.price', 'required', function ($input) { 
            return $input->type === 'variant' && count($input->var1_val); 
        });

        $validator->sometimes('variant.*.cost', 'required', function ($input) { 
            return $input->type === 'variant' && count($input->var1_val); 
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
            'name' => __('common.name'),
            'category_id' => __('menu.category'),
            'brand_id' => __('mmenu.brand'),
            'type' => __('product.product_type'),
            'unit_id' => __('product.base_unit'),
            'var1_name' => __('product.variant_name', ['no' => 1]),
            'var1_val' => __('product.variant_value', ['no' => 1]),
            'var2_name' => __('product.variant_name', ['no' => 2]),
            'var2_val' => __('product.variant_value', ['no' => 2]),
            'variant.*.price' => __('product.price'),
            'variant.*.cost' => __('product.cost'),
            'variant.*.sku' => __('product.sku'),
        ];
    }
}
