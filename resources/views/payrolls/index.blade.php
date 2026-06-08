@extends('layouts.app')
@section('title', 'Fiches de paie')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Fiches de paie</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gestion de la paie du personnel</p>
        </div>
        <a href="{{ route('payrolls.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Nouvelle fiche
        </a>
    </div>

    {{-- Filtres mois/année --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex gap-3">
            <select name="month" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $i => $m)
                <option value="{{ $i+1 }}" {{ $month == $i+1 ? 'selected':'' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="year" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                @for($y = now()->year; $y >= now()->year-2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Filtrer</button>
        </div>
    </form>

    {{-- Totaux --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total payé</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalPaid, 0, ',', ' ') }} F</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">En attente</p>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($totalPending, 0, ',', ' ') }} F</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Personnel</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Type</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Net salary</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payrolls as $payroll)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-800">{{ $payroll->payable->full_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $payroll->payroll_number }}</p>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ str_contains($payroll->payable_type, 'Teacher') ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ str_contains($payroll->payable_type, 'Teacher') ? 'Enseignant' : 'Personnel' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">
                        {{ number_format($payroll->net_salary, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $payroll->status === 'paid' ? 'Payé' : 'En attente' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('payrolls.show', $payroll) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('payrolls.payslip', $payroll) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Aucune fiche pour cette période</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($payrolls->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $payrolls->links() }}</div>
        @endif
    </div>
</div>
@endsection
