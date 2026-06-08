<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payable_id'      => 'required|integer',
            'payable_type'    => 'required|in:teacher,staff',
            'month'           => 'required|integer|between:1,12',
            'year'            => 'required|integer|min:2000|max:2100',
            'base_salary'     => 'required|numeric|min:0',
            'bonuses'         => 'nullable|numeric|min:0',
            'deductions'      => 'nullable|numeric|min:0',
            'payment_method'  => 'required|in:cash,virement,mobile_money,cheque',
            'payment_date'    => 'nullable|date',
            'notes'           => 'nullable|string',
        ];
    }
}
