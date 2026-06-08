@extends('layouts.app')
@section('title', 'Années scolaires')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Années scolaires</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gestion des années et des tarifs</p>
        </div>
        <a href="{{ route('school-years.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Nouvelle année
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Année</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Période</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Inscription</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mensualité</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Élèves</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($schoolYears as $year)
                <tr class="{{ $year->is_active ? 'bg-emerald-50/40' : '' }} hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($year->is_active)
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 animate-pulse"></span>
                            @else
                            <span class="w-2 h-2 rounded-full bg-gray-200 shrink-0"></span>
                            @endif
                            <span class="font-bold text-gray-900">{{ $year->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-xs text-gray-500">
                        {{ $year->start_date->format('d/m/Y') }} → {{ $year->end_date->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                        {{ number_format($year->inscription_fee, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                        {{ number_format($year->monthly_fee, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center hidden md:table-cell text-gray-600">
                        {{ $year->students_count ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($year->is_active)
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-100 text-emerald-700">Active</span>
                        @else
                        <form method="POST" action="{{ route('school-years.activate', $year) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 transition cursor-pointer">
                                Activer
                            </button>
                        </form>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('school-years.edit', $year) }}" class="text-xs text-gray-500 hover:text-indigo-600 font-medium">Modifier</a>
                            @if($year->is_active)
                            <form method="POST" action="{{ route('school-years.close', $year) }}"
                                  onsubmit="return confirm('Fermer l\'année {{ $year->name }} ? Elle sera archivée et plus aucun paiement ne sera lié à elle par défaut.')">
                                @csrf
                                <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                    Fermer l'année
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">Aucune année scolaire</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
