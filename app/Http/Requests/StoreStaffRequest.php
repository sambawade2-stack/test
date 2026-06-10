<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;

        return [
            'first_name'    => 'required|string|max:60',
            'last_name'     => 'required|string|max:60',
            'email'         => 'nullable|email|max:100|unique:staff,email,' . $staffId,
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'gender'        => 'required|in:M,F',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality'   => 'nullable|string|max:50',
            'photo'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'category'      => 'required|in:administratif,appoint',
            'position'      => 'required|string|max:80',
            'hire_date'     => 'required|date',
            'base_salary'   => 'required|numeric|min:0',
            'contract_type' => 'required|in:permanent,contractuel,stagiaire',
            'status'        => 'required|in:active,inactive,leave',
            'notes'         => 'nullable|string',
        ];
    }
}
