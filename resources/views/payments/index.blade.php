@extends('layouts.app')
@section('title', 'Paiements')

@section('content')
<div class="space-y-5">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Paiements</h1>
            <p class="text-sm text-gray-400 mt-0.5">Historique de tous les paiements</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('payments.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter Excel
            </a>
            <a href="{{ route('payments.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                + Nouveau paiement
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nom, prénom, matricule..."
                       class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            </div>
            <select name="payment_type" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                <option value="">Tous types</option>
                <option value="inscription" {{ request('payment_type') === 'inscription' ? 'selected' : '' }}>Inscription</option>
                <option value="mensualite"  {{ request('payment_type') === 'mensualite'  ? 'selected' : '' }}>Mensualité</option>
            </select>
            <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                <option value="">Tous statuts</option>
                <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Payé</option>
                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partiel</option>
                <option value="unpaid"  {{ request('status') === 'unpaid'  ? 'selected' : '' }}>Impayé</option>
            </select>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2 text-sm font-medium transition">
                Filtrer
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reçu N°</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Élève</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Type</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Période</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs text-gray-500">{{ $payment->receipt_number }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $payment->student->full_name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $payment->classroom->name ?? '' }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $payment->payment_type === 'inscription' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $payment->payment_type === 'inscription' ? 'Inscription' : 'Mensualité' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-xs text-gray-500">
                        @if($payment->payment_type === 'mensualite')
                            {{ $payment->month_label }} {{ $payment->year }}
                        @else
                            {{ $payment->schoolYear->name ?? '—' }}
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div>
                            <p class="font-bold text-gray-900">{{ number_format($payment->amount_paid, 0, ',', ' ') }} F</p>
                            @if($payment->balance > 0)
                            <p class="text-xs text-red-500">-{{ number_format($payment->balance, 0, ',', ' ') }} F reste</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $payment->status === 'paid'    ? 'bg-emerald-50 text-emerald-700' :
                               ($payment->status === 'partial' ? 'bg-amber-50 text-amber-700'   : 'bg-red-50 text-red-700') }}">
                            {{ $payment->status === 'paid' ? 'Payé' : ($payment->status === 'partial' ? 'Partiel' : 'Impayé') }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('payments.show', $payment) }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('payments.receipt', $payment) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">PDF</a>
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
                        <p class="text-gray-400 text-sm">Aucun paiement trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
