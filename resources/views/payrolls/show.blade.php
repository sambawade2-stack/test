@extends('layouts.app')
@section('title', 'Fiche ' . $payroll->payroll_number)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('payrolls.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Fiche N° {{ $payroll->payroll_number }}</h1>
            <p class="text-sm text-gray-400">{{ $payroll->month_label }} {{ $payroll->year }}</p>
        </div>
        <div class="flex gap-2">
            @if($payroll->status === 'pending')
            <form method="POST" action="{{ route('payrolls.mark-paid', $payroll) }}" class="inline flex items-center gap-2">
                @csrf
                <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                       class="border border-gray-200 rounded-xl px-3 py-1.5 text-sm outline-none">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-xl text-sm font-medium transition">
                    Marquer payé
                </button>
            </form>
            @endif
            <a href="{{ route('payrolls.payslip', $payroll) }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center text-xl font-bold text-white">
                {{ strtoupper(substr($payroll->payable->first_name ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $payroll->payable->full_name ?? '—' }}</p>
                <p class="text-sm text-gray-500">
                    {{ str_contains($payroll->payable_type, 'Teacher') ? ($payroll->payable->subject ?? '') : ($payroll->payable->position ?? '') }}
                </p>
                <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full font-medium
                    {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $payroll->status === 'paid' ? 'Payé' : 'En attente' }}
                </span>
            </div>
        </div>

        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Salaire de base</dt><dd class="font-semibold">{{ number_format($payroll->base_salary, 0, ',', ' ') }} F</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Primes</dt><dd class="font-semibold text-emerald-600">+ {{ number_format($payroll->bonuses, 0, ',', ' ') }} F</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Retenues</dt><dd class="font-semibold text-red-500">- {{ number_format($payroll->deductions, 0, ',', ' ') }} F</dd></div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <dt class="font-bold text-gray-800">Net à payer</dt>
                <dd class="text-xl font-bold text-indigo-600">{{ number_format($payroll->net_salary, 0, ',', ' ') }} F</dd>
            </div>
            @if($payroll->payment_date)
            <div class="flex justify-between text-sm"><dt class="text-gray-500">Date de paiement</dt><dd class="font-medium">{{ $payroll->payment_date->format('d/m/Y') }}</dd></div>
            @endif
        </dl>
    </div>
</div>
@endsection
