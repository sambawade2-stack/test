@extends('layouts.app')
@section('title', 'Nouvelle classe')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('classrooms.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Nouvelle classe</h1>
    </div>

    <form method="POST" action="{{ route('classrooms.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            <div class="p-6 grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Niveau <span class="text-red-500">*</span></label>
                    <select name="level" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white" onchange="setCycle(this)">
                        <option value="">-- Choisir --</option>
                        @foreach(['6ème','5ème','4ème','3ème'] as $lvl)
                        <option value="{{ $lvl }}" data-cycle="collège" {{ old('level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                        @foreach(['2nde','1ère','Terminale'] as $lvl)
                        <option value="{{ $lvl }}" data-cycle="lycée" {{ old('level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                    @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Section</label>
                    <input type="text" name="section" value="{{ old('section') }}"
                           placeholder="A, B, S, L..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cycle <span class="text-red-500">*</span></label>
                    <select name="cycle" id="cycle" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                        <option value="collège" {{ old('cycle','collège') === 'collège' ? 'selected':'' }}>Collège</option>
                        <option value="lycée"   {{ old('cycle') === 'lycée' ? 'selected':'' }}>Lycée</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Capacité max <span class="text-red-500">*</span></label>
                    <input type="number" name="max_students" value="{{ old('max_students', 45) }}"
                           min="1" max="100" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire <span class="text-red-500">*</span></label>
                    <select name="school_year_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                        @foreach($schoolYears as $year)
                        <option value="{{ $year->id }}"
                                {{ old('school_year_id', $currentYear?->id) == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Professeur principal</label>
                    <input type="text" name="main_teacher" value="{{ old('main_teacher') }}"
                           placeholder="Nom du professeur principal"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
            </div>

            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('classrooms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Créer la classe
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function setCycle(select) {
    const opt = select.options[select.selectedIndex];
    if (opt.dataset.cycle) {
        document.getElementById('cycle').value = opt.dataset.cycle;
    }
}
</script>
@endsection
