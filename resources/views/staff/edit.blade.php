@extends('layouts.app')
@section('title', 'Modifier ' . $staff->full_name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('staff.show', $staff) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Modifier {{ $staff->full_name }}</h1>
    </div>
    <form method="POST" action="{{ route('staff.update', $staff) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            <div class="p-6 grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Nom *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie *</label>
                    <select name="category" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                        <option value="administratif" {{ old('category',$staff->category) === 'administratif' ? 'selected':'' }}>Administratif</option>
                        <option value="appoint"       {{ old('category',$staff->category) === 'appoint' ? 'selected':'' }}>Appoint</option>
                    </select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Poste *</label>
                    <input type="text" name="position" value="{{ old('position', $staff->position) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Salaire base *</label>
                    <input type="number" name="base_salary" value="{{ old('base_salary', $staff->base_salary) }}" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                    <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                        <option value="active"   {{ old('status',$staff->status) === 'active' ? 'selected':'' }}>Actif</option>
                        <option value="inactive" {{ old('status',$staff->status) === 'inactive' ? 'selected':'' }}>Inactif</option>
                        <option value="leave"    {{ old('status',$staff->status) === 'leave' ? 'selected':'' }}>En congé</option>
                    </select></div>
            </div>
            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('staff.show', $staff) }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection
