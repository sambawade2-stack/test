@extends('layouts.app')
@section('title', $teacher->full_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('teachers.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 flex-1">{{ $teacher->full_name }}</h1>
        <a href="{{ route('teachers.edit', $teacher) }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition">Modifier</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-5 mb-6">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center text-2xl font-bold text-white">
                {{ strtoupper(substr($teacher->first_name, 0, 1)) }}
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $teacher->full_name }}</p>
                <p class="text-gray-500">{{ $teacher->subject }}</p>
                <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full font-medium
                    {{ $teacher->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($teacher->status) }}
                </span>
            </div>
        </div>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Matricule</dt><dd class="font-semibold text-gray-800">{{ $teacher->employee_number }}</dd></div>
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Contrat</dt><dd class="font-semibold text-gray-800">{{ ucfirst($teacher->contract_type) }}</dd></div>
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Email</dt><dd class="text-gray-700">{{ $teacher->email ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Téléphone</dt><dd class="text-gray-700">{{ $teacher->phone ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Date embauche</dt><dd class="text-gray-700">{{ $teacher->hire_date->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Salaire de base</dt><dd class="font-bold text-gray-900">{{ number_format($teacher->base_salary, 0, ',', ' ') }} FCFA</dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-800">Fiches de paie</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('teachers.payroll-history', $teacher) }}"
                   class="text-xs text-gray-600 hover:text-indigo-700 font-medium border border-gray-200 hover:border-indigo-300 px-3 py-1.5 rounded-lg transition">
                    Historique & PDF
                </a>
                <a href="{{ route('payrolls.create') }}" class="text-xs text-indigo-600 hover:underline">+ Nouvelle</a>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Période</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Net payé</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Statut</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($teacher->payrolls as $payroll)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3">{{ $payroll->month_label }} {{ $payroll->year }}</td>
                    <td class="px-5 py-3 text-right font-semibold">{{ number_format($payroll->net_salary, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $payroll->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $payroll->status === 'paid' ? 'Payé' : 'En attente' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400 text-sm">Aucune fiche de paie</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
