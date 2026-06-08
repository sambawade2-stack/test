@extends('layouts.app')
@section('title', 'Modifier ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Modifier {{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ $user->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            @include('users._form')
            <div class="px-6 py-4 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
