@extends('layouts.app')
@section('title', 'Historique des scans')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Historique des passages</h1>
            <p class="text-sm text-gray-400 mt-0.5">Journal des badges scannés à l'entrée</p>
        </div>
        <a href="{{ route('scan.index') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Scanner
        </a>
    </div>

    {{-- Stats du jour --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Passages aujourd'hui</p>
            <p class="text-3xl font-bold text-gray-900">{{ $todayTotal }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Refusés aujourd'hui</p>
            <p class="text-3xl font-bold {{ $todayDenied > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $todayDenied }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                <select name="filter" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    <option value="">Tous</option>
                    <option value="denied" {{ request('filter') === 'denied' ? 'selected' : '' }}>Refusés uniquement</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Filtrer</button>
            @if(request()->hasAny(['date','filter']))
            <a href="{{ route('scan.history') }}" class="text-xs text-gray-400 hover:text-gray-600 py-2">Réinitialiser</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Heure</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Élève</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Classe</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Scanné par</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-gray-800">{{ $log->scanned_at->format('H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $log->scanned_at->locale('fr')->isoFormat('D MMM') }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($log->found && $log->student)
                            <a href="{{ route('students.show', $log->student) }}" class="font-semibold text-gray-800 hover:text-indigo-700">
                                {{ $log->student->full_name }}
                            </a>
                            <p class="text-xs text-gray-400 font-mono">{{ $log->matricule }}</p>
                        @else
                            <span class="text-gray-500">Inconnu</span>
                            <p class="text-xs text-gray-400 font-mono">{{ $log->matricule ?? '—' }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-600">{{ $log->classroom ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        @if(!$log->found)
                            <span class="inline-flex text-xs px-2.5 py-1 rounded-full font-medium bg-amber-50 text-amber-700">Introuvable</span>
                        @elseif($log->in_order)
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En règle
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-red-50 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Refusé
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-gray-500">{{ $log->scanner?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400">Aucun scan enregistré</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
