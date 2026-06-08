@extends('layouts.app')
@section('title', 'Classes')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Classes</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $classrooms->count() }} classe(s) au total</p>
        </div>
        <a href="{{ route('classrooms.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Nouvelle classe
        </a>
    </div>

    {{-- Filtre année --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-600">Année scolaire :</label>
            <select name="school_year_id" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                <option value="">Toutes</option>
                @foreach($schoolYears as $year)
                <option value="{{ $year->id }}" {{ $schoolYearId == $year->id ? 'selected' : '' }}>
                    {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Grille par cycle --}}
    @php
        $college = $classrooms->where('cycle', 'collège');
        $lycee   = $classrooms->where('cycle', 'lycée');
    @endphp

    @if($college->count())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center text-indigo-600 text-xs">C</span>
            Collège
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($college as $classroom)
            @include('classrooms._card', ['classroom' => $classroom])
            @endforeach
        </div>
    </div>
    @endif

    @if($lycee->count())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-6 h-6 bg-violet-100 rounded-md flex items-center justify-center text-violet-600 text-xs">L</span>
            Lycée
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($lycee as $classroom)
            @include('classrooms._card', ['classroom' => $classroom])
            @endforeach
        </div>
    </div>
    @endif

    @if($classrooms->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-14 text-center">
        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <p class="font-semibold text-gray-500">Aucune classe trouvée</p>
        <a href="{{ route('classrooms.create') }}" class="text-sm text-indigo-600 hover:underline mt-1 block">
            Créer la première classe →
        </a>
    </div>
    @endif

</div>
@endsection
