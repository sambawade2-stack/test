@extends('layouts.app')
@section('title', 'Nouvel élève')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Inscrire un nouvel élève</h1>
    </div>

    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            {{-- Photo --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Photo</h2>
                @include('students._photo')
            </div>

            {{-- Identité --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Identité de l'élève</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de naissance <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('date_of_birth')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lieu de naissance</label>
                        <input type="text" name="place_of_birth" value="{{ old('place_of_birth') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Genre <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nationalité</label>
                        <input type="text" name="nationality" value="{{ old('nationality', 'Sénégalaise') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            {{-- Scolarité --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Scolarité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe <span class="text-red-500">*</span></label>
                        <select name="classroom_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            <option value="">-- Choisir --</option>
                            @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('classroom_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire <span class="text-red-500">*</span></label>
                        <select name="school_year_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            @foreach($schoolYears as $year)
                            <option value="{{ $year->id }}" {{ old('school_year_id', $currentYear?->id) == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'inscription <span class="text-red-500">*</span></label>
                        <input type="date" name="enrolled_at" value="{{ old('enrolled_at', now()->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">École précédente</label>
                        <input type="text" name="previous_school" value="{{ old('previous_school') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            {{-- Parent --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Tuteur / Parent</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Relation</label>
                        <select name="parent_relation" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            <option value="">--</option>
                            <option value="père"   {{ old('parent_relation') === 'père'   ? 'selected' : '' }}>Père</option>
                            <option value="mère"   {{ old('parent_relation') === 'mère'   ? 'selected' : '' }}>Mère</option>
                            <option value="tuteur" {{ old('parent_relation') === 'tuteur' ? 'selected' : '' }}>Tuteur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Inscrire l'élève
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
