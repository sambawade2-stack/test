<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'first_name'       => 'required|string|max:60',
            'last_name'        => 'required|string|max:60',
            'date_of_birth'    => 'required|date|before:today',
            'place_of_birth'   => 'nullable|string|max:80',
            'gender'           => 'required|in:M,F',
            'photo'            => 'nullable|image|max:2048',
            'address'          => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'nationality'      => 'nullable|string|max:50',
            'parent_name'      => 'nullable|string|max:100',
            'parent_phone'     => 'nullable|string|max:20',
            'parent_email'     => 'nullable|email|max:100',
            'parent_relation'  => 'nullable|string|max:30',
            'classroom_id'     => 'required|exists:classrooms,id',
            'school_year_id'   => 'required|exists:school_years,id',
            'enrolled_at'      => 'required|date',
            'previous_school'  => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'    => 'Le prénom est obligatoire.',
            'last_name.required'     => 'Le nom est obligatoire.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.before'   => 'La date de naissance doit être dans le passé.',
            'gender.required'        => 'Le genre est obligatoire.',
            'gender.in'              => 'Le genre doit être M ou F.',
            'classroom_id.required'  => 'La classe est obligatoire.',
            'classroom_id.exists'    => 'La classe sélectionnée est invalide.',
            'school_year_id.required'=> 'L\'année scolaire est obligatoire.',
            'enrolled_at.required'   => 'La date d\'inscription est obligatoire.',
        ];
    }
}
