<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
class CustomerRequest extends FormRequest
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
            'mobile' => 'required|numeric',
            'email' => 'required|email',
        ];
    }

    

    public function withValidator(Validator $validator)
    {

        // $validator->sometimes('individual', 'required', function ($input) { 
        //     return $input->type === 'variant'; 
        // });

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
            'mobile' => __('common.mobile'),
            'email' => __('common.email'),
        ];
    }
}
