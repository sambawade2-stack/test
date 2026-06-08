@extends('layouts.app')
@section('title', 'Impayés')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Élèves impayés</h1>
            <p class="text-sm text-gray-400 mt-0.5">Mensualités non réglées</p>
        </div>
        @if(count($students) > 0)
        <a href="{{ route('payments.unpaid.report', request()->query()) }}"
           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            Exporter PDF
        </a>
        @endif
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Mois</label>
                <select name="month" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ $month == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Année</label>
                <select name="year" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    @for($y = now()->year; $y >= now()->year-2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Classe</label>
                <select name="classroom_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    <option value="">Toutes</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2 text-sm font-medium transition">
                    Filtrer
                </button>
            </div>
        </div>
    </form>

    @php
    $monthLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
    @endphp

    @if(count($students) > 0)
    <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-red-800">{{ count($students) }} élève(s) n'ont pas payé</p>
            <p class="text-sm text-red-600">{{ $monthLabels[$month] ?? $month }} {{ $year }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Matricule</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Élève</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Classe</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Parent</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($students as $student)
                <tr class="hover:bg-red-50/30 transition">
                    <td class="px-5 py-3.5 font-mono text-xs text-gray-400">{{ $student->registration_number }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <span class="text-red-600 text-xs font-bold">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                            </div>
                            <p class="font-semibold text-gray-800">{{ $student->full_name }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-600">{{ $student->classroom->name ?? '—' }}</td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <p class="text-gray-600">{{ $student->parent_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $student->parent_phone ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('students.show', $student) }}" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Profil</a>
                            <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                               class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-medium transition">
                                Encaisser
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-emerald-700">Tous les élèves ont payé !</p>
                        <p class="text-xs text-gray-400 mt-1">Aucun impayé pour cette période</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
