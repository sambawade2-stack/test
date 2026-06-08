@extends('layouts.app')
@php
    $employee = $employeeType === 'teacher' ? $teacher : $staff;
    $backRoute = $employeeType === 'teacher'
        ? route('teachers.show', $employee)
        : route('staff.show', $employee);
    $pdfRoute  = $employeeType === 'teacher'
        ? route('teachers.payroll-history.pdf', $employee)
        : route('staff.payroll-history.pdf', $employee);
    $selfRoute = $employeeType === 'teacher'
        ? route('teachers.payroll-history', $employee)
        : route('staff.payroll-history', $employee);

    $monthLabels = [
        1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',
        5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',
        9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre',
    ];
@endphp
@section('title', 'Historique paie — ' . $employee->full_name)

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ $backRoute }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Historique des paies</h1>
            <p class="text-sm text-gray-400">{{ $employee->full_name }} · {{ $employee->employee_number }}</p>
        </div>
        {{-- Bouton export PDF --}}
        <a href="{{ $pdfRoute }}?{{ http_build_query(request()->query()) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exporter PDF
        </a>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ $selfRoute }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Personnaliser la période</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Année</label>
                <select name="year" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    <option value="">Toutes</option>
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mois de début</label>
                <select name="month_from" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    <option value="">--</option>
                    @foreach($monthLabels as $n => $m)
                    <option value="{{ $n }}" {{ request('month_from') == $n ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mois de fin</label>
                <select name="month_to" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    <option value="">--</option>
                    @foreach($monthLabels as $n => $m)
                    <option value="{{ $n }}" {{ request('month_to') == $n ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2 text-sm font-medium transition">
                    Filtrer
                </button>
                @if(request()->hasAny(['year','month_from','month_to']))
                <a href="{{ $selfRoute }}" class="text-xs text-gray-400 hover:text-gray-600 py-2">Réinitialiser</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Cartes résumé --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Fiches générées</p>
            <p class="text-3xl font-bold text-gray-900">{{ $payrolls->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total net</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalNet, 0, ',', ' ') }} F</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total payé</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalPaid, 0, ',', ' ') }} F</p>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Période</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Base</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Primes</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Retenues</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Net</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payrolls as $payroll)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-800">{{ $monthLabels[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $payroll->payroll_number }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-right text-gray-700">{{ number_format($payroll->base_salary, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3.5 text-right hidden md:table-cell text-emerald-600">
                        {{ $payroll->bonuses > 0 ? '+ ' . number_format($payroll->bonuses, 0, ',', ' ') . ' F' : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-right hidden md:table-cell text-red-500">
                        {{ $payroll->deductions > 0 ? '- ' . number_format($payroll->deductions, 0, ',', ' ') . ' F' : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">
                        {{ number_format($payroll->net_salary, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $payroll->status === 'paid' ? 'Payé' : 'En attente' }}
                        </span>
                        @if($payroll->payment_date)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $payroll->payment_date->format('d/m/Y') }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('payrolls.show', $payroll) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('payrolls.payslip', $payroll) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-gray-400">Aucune fiche de paie pour cette période</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($payrolls->count() > 0)
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="4" class="px-5 py-3 text-sm font-semibold text-gray-700 hidden md:table-cell">
                        Total ({{ $payrolls->count() }} fiche(s))
                    </td>
                    <td class="px-5 py-3 text-right text-sm font-bold text-gray-900">
                        {{ number_format($totalNet, 0, ',', ' ') }} F
                    </td>
                    <td colspan="2" class="px-5 py-3 text-center">
                        <span class="text-xs text-emerald-600 font-semibold">
                            Payé : {{ number_format($totalPaid, 0, ',', ' ') }} F
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
@endsection
