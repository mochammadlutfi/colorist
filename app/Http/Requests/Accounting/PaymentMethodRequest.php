<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PaymentMethodRequest extends FormRequest
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
            'type' => 'required',
            'account_id' => 'required',
        ];
    }

    public function withValidator(Validator $validator)
    {

        $validator->sometimes('bank_id', 'required', function ($input) { 
            return $input->type === 'bank';
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
            'type' => __('common.type'),
            'account_id' => __('account.default_account'),
            'bank_id' => __('menu.bank'),
        ];
    }
}
