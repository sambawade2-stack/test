@extends('layouts.app')

@section('title', 'Élèves')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Élèves</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.export', request()->query()) }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter Excel
            </a>
            @if(auth()->user()->canAccess('students_manage'))
            <a href="{{ route('students.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouvel élève
            </a>
            @endif
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom, prénom, matricule…"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Classe</label>
            <select name="classroom_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">Toutes les classes</option>
                @foreach($classrooms as $c)
                <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Année scolaire</label>
            <select name="school_year_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none">
                <option value="">Toutes</option>
                @foreach($schoolYears as $sy)
                <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>
                    {{ $sy->name }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            Filtrer
        </button>
        @if(request()->hasAny(['search', 'classroom_id', 'school_year_id']))
        <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Réinitialiser</a>
        @endif
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Matricule</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Élève</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Classe</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Genre</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Parent</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->registration_number }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($student->photo)
                                <img src="{{ asset('storage/'.$student->photo) }}" alt=""
                                     class="w-9 h-9 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-9 h-9 rounded-full {{ $student->gender === 'F' ? 'bg-pink-100' : 'bg-blue-100' }} flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold {{ $student->gender === 'F' ? 'text-pink-600' : 'text-blue-600' }}">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $student->date_of_birth?->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $student->classroom?->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs {{ $student->gender === 'M' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                            {{ $student->gender === 'M' ? 'Garçon' : 'Fille' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <div>{{ $student->parent_name }}</div>
                        <div class="text-xs text-gray-400">{{ $student->parent_phone }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('students.show', $student) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Voir</a>
                            <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                               class="text-green-600 hover:text-green-800 text-xs font-medium">Payer</a>
                            <a href="{{ route('students.edit', $student) }}"
                               class="text-gray-500 hover:text-gray-700 text-xs">Modifier</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        Aucun élève trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $students->links() }}
</div>
@endsection
