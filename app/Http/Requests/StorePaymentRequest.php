<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'student_id'      => 'required|exists:students,id',
            'school_year_id'  => 'required|exists:school_years,id',
            'classroom_id'    => 'required|exists:classrooms,id',
            'payment_type'    => 'required|in:inscription,mensualite',
            'amount_due'      => 'required|numeric|min:0',
            'amount_paid'     => 'required|numeric|min:0',
            'payment_method'  => 'required|in:cash,virement,mobile_money,cheque',
            'payment_date'    => 'required|date',
            'notes'           => 'nullable|string',
        ];

        if ($this->payment_type === 'mensualite') {
            $rules['month'] = 'required|integer|between:1,12';
            $rules['year']  = 'required|integer|min:2000|max:2100';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'student_id.required'     => 'L\'élève est obligatoire.',
            'student_id.exists'       => 'L\'élève sélectionné est invalide.',
            'payment_type.required'   => 'Le type de paiement est obligatoire.',
            'payment_type.in'         => 'Le type doit être inscription ou mensualité.',
            'amount_due.required'     => 'Le montant dû est obligatoire.',
            'amount_paid.required'    => 'Le montant payé est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
            'payment_date.required'   => 'La date de paiement est obligatoire.',
            'month.required'          => 'Le mois est obligatoire pour une mensualité.',
            'month.between'           => 'Le mois doit être entre 1 et 12.',
            'year.required'           => 'L\'année est obligatoire pour une mensualité.',
        ];
    }
}
