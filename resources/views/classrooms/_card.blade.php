@php
    $count   = $classroom->students_count ?? 0;
    $max     = $classroom->max_students;
    $percent = $max > 0 ? min(100, round(($count / $max) * 100)) : 0;
    $color   = $percent >= 90 ? 'red' : ($percent >= 70 ? 'amber' : 'emerald');
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-5 group">
    <div class="flex items-start justify-between mb-4">
        <div class="w-10 h-10 rounded-xl
            {{ $classroom->cycle === 'lycée' ? 'bg-violet-100' : 'bg-indigo-100' }}
            flex items-center justify-center font-bold text-sm
            {{ $classroom->cycle === 'lycée' ? 'text-violet-700' : 'text-indigo-700' }}">
            {{ strtoupper(substr($classroom->level, 0, 2)) }}
        </div>
        <a href="{{ route('classrooms.edit', $classroom) }}"
           class="text-gray-300 hover:text-gray-500 opacity-0 group-hover:opacity-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    </div>

    <a href="{{ route('classrooms.show', $classroom) }}">
        <h3 class="font-bold text-gray-900 text-lg group-hover:text-indigo-700 transition">{{ $classroom->name }}</h3>
        @if($classroom->main_teacher)
        <p class="text-xs text-gray-400 mt-0.5 truncate">Prof. principal : {{ $classroom->main_teacher }}</p>
        @endif
    </a>

    {{-- Barre de remplissage --}}
    <div class="mt-4">
        <div class="flex justify-between text-xs mb-1.5">
            <span class="text-gray-500 font-medium">{{ $count }} élèves</span>
            <span class="text-gray-400">/ {{ $max }}</span>
        </div>
        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all
                {{ $color === 'red' ? 'bg-red-400' : ($color === 'amber' ? 'bg-amber-400' : 'bg-emerald-400') }}"
                style="width: {{ $percent }}%">
            </div>
        </div>
    </div>

    <div class="mt-3 flex items-center justify-between">
        <span class="text-xs px-2 py-0.5 rounded-full font-medium
            {{ $classroom->cycle === 'lycée' ? 'bg-violet-50 text-violet-700' : 'bg-indigo-50 text-indigo-700' }}">
            {{ ucfirst($classroom->cycle) }}
        </span>
        <div class="flex items-center gap-3">
            @if($count > 0)
            <a href="{{ route('classrooms.badges', $classroom) }}"
               class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-800 font-medium"
               title="Imprimer les badges">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Badges
            </a>
            @endif
            <a href="{{ route('classrooms.show', $classroom) }}"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                Voir →
            </a>
        </div>
    </div>
</div>
