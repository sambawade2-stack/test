<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->id;

        return [
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'required|string|max:60',
            'email'          => 'nullable|email|max:100|unique:teachers,email,' . $teacherId,
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:255',
            'gender'         => 'required|in:M,F',
            'date_of_birth'  => 'nullable|date|before:today',
            'nationality'    => 'nullable|string|max:50',
            'photo'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'hire_date'      => 'required|date',
            'subject'        => 'required|string|max:80',
            'qualification'  => 'nullable|string|max:100',
            'base_salary'    => 'required|numeric|min:0',
            'contract_type'  => 'required|in:permanent,vacataire,stagiaire',
            'status'         => 'required|in:active,inactive,leave',
            'notes'          => 'nullable|string',
        ];
    }
}
