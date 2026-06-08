@extends('layouts.app')
@section('title', $classroom->name)

@section('content')
<div class="space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('classrooms.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 flex-1">{{ $classroom->name }}</h1>
        <div class="flex gap-2">
            @if($classroom->students->count() > 0)
            <a href="{{ route('classrooms.badges', $classroom) }}"
               class="inline-flex items-center gap-1.5 text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                Imprimer les badges
            </a>
            @endif
            <a href="{{ route('students.create') }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-medium transition">
                + Élève
            </a>
            <a href="{{ route('classrooms.edit', $classroom) }}"
               class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition">
                Modifier
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $classroom->students->count() }}</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Élèves actifs</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $classroom->max_students }}</p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Capacité max</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold {{ $classroom->isFull() ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $classroom->max_students - $classroom->students->count() }}
            </p>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">Places restantes</p>
        </div>
    </div>

    {{-- Info classe --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Cycle</dt>
                <dd class="font-semibold text-gray-800">{{ ucfirst($classroom->cycle) }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Niveau</dt>
                <dd class="font-semibold text-gray-800">{{ $classroom->level }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Section</dt>
                <dd class="font-semibold text-gray-800">{{ $classroom->section ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Prof. principal</dt>
                <dd class="font-semibold text-gray-800">{{ $classroom->main_teacher ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Liste élèves --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-800">Élèves ({{ $classroom->students->count() }})</h2>
            <a href="{{ route('students.index', ['classroom_id' => $classroom->id]) }}"
               class="text-xs text-indigo-600 hover:underline">Voir tout</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Matricule</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom & Prénom</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Genre</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($classroom->students as $student)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 font-mono text-xs text-gray-400">{{ $student->registration_number }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full {{ $student->gender === 'F' ? 'bg-pink-100' : 'bg-blue-100' }} flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold {{ $student->gender === 'F' ? 'text-pink-600' : 'text-blue-600' }}">
                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $student->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $student->gender === 'F' ? 'bg-pink-50 text-pink-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $student->gender === 'F' ? 'Fille' : 'Garçon' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('students.show', $student) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('payments.create', ['student_id' => $student->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Payer</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-gray-400">Aucun élève dans cette classe</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
