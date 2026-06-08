@extends('layouts.app')
@section('title', 'Modifier ' . $student->full_name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('students.show', $student) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Modifier {{ $student->full_name }}</h1>
    </div>

    <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            {{-- Photo --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Photo</h2>
                @include('students._photo', ['existingPhoto' => $student->photo])
            </div>

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Identité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom *</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de naissance *</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Genre *</label>
                        <select name="gender" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            <option value="M" {{ old('gender', $student->gender) === 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('gender', $student->gender) === 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $student->address) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Scolarité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Classe *</label>
                        <select name="classroom_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id', $student->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Année scolaire *</label>
                        <select name="school_year_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            @foreach($schoolYears as $year)
                            <option value="{{ $year->id }}" {{ old('school_year_id', $student->school_year_id) == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'inscription *</label>
                        <input type="date" name="enrolled_at" value="{{ old('enrolled_at', $student->enrolled_at->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Tuteur / Parent</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="parent_email" value="{{ old('parent_email', $student->parent_email) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('students.show', $student) }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
