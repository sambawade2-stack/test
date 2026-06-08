@extends('layouts.app')
@section('title', 'Présences — ' . $student->full_name)

@php
$monthLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('students.show', $student) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Présences — {{ $student->full_name }}</h1>
            <p class="text-sm text-gray-400">{{ $student->classroom?->name }} · {{ $student->registration_number }}</p>
        </div>
        @if(auth()->user()->canAccess('certificates'))
        <a href="{{ route('students.certificate.assiduite', $student) }}?month={{ $month }}&year={{ $year }}"
           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-xl font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Certificat d'assiduité
        </a>
        @endif
    </div>

    {{-- Filtre mois/année --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex gap-3">
            <select name="month" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                @foreach($monthLabels as $n => $m)
                <option value="{{ $n }}" {{ $month == $n ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="year" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                @for($y = now()->year; $y >= now()->year-2; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Voir</button>
        </div>
    </form>

    {{-- Taux d'assiduité --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-700">Taux de présence — {{ $monthLabels[$month] }} {{ $year }}</span>
            <span class="text-2xl font-bold {{ $stats['rate'] >= 90 ? 'text-emerald-600' : ($stats['rate'] >= 75 ? 'text-amber-600' : 'text-red-600') }}">
                {{ $stats['rate'] }}%
            </span>
        </div>
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full {{ $stats['rate'] >= 90 ? 'bg-emerald-500' : ($stats['rate'] >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                 style="width: {{ $stats['rate'] }}%"></div>
        </div>
        <div class="grid grid-cols-4 gap-3 mt-4 text-center">
            <div><p class="text-xl font-bold text-emerald-600">{{ $stats['present'] }}</p><p class="text-xs text-gray-400">Présent</p></div>
            <div><p class="text-xl font-bold text-red-600">{{ $stats['absent'] }}</p><p class="text-xs text-gray-400">Absent</p></div>
            <div><p class="text-xl font-bold text-amber-600">{{ $stats['late'] }}</p><p class="text-xs text-gray-400">Retard</p></div>
            <div><p class="text-xl font-bold text-sky-600">{{ $stats['excused'] }}</p><p class="text-xs text-gray-400">Excusé</p></div>
        </div>
    </div>

    {{-- Détail jour par jour --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Motif</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $r)
                @php
                    $badge = ['present'=>['Présent','emerald'],'absent'=>['Absent','red'],'late'=>['Retard','amber'],'excused'=>['Excusé','sky']][$r->status];
                @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-gray-700">{{ $r->date->locale('fr')->isoFormat('ddd D MMM') }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-{{ $badge[1] }}-50 text-{{ $badge[1] }}-700">{{ $badge[0] }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $r->reason ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400">Aucune présence enregistrée ce mois</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
