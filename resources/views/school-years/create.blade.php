@extends('layouts.app')
@section('title', 'Nouvelle année scolaire')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('school-years.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Nouvelle année scolaire</h1>
    </div>

    <form method="POST" action="{{ route('school-years.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            <div class="p-6 grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de l'année <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Ex: 2025-2026" required maxlength="20"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de fin <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Frais d'inscription (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" name="inscription_fee" value="{{ old('inscription_fee', 25000) }}"
                           min="0" step="500" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mensualité (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_fee" value="{{ old('monthly_fee', 15000) }}"
                           min="0" step="500" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>

            </div>
            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('school-years.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Créer l'année
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
