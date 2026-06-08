@extends('layouts.app')
@section('title', 'Personnel')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Personnel</h1>
            <p class="text-sm text-gray-400 mt-0.5">Administratif & d'appoint</p>
        </div>
        <a href="{{ route('staff.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Ajouter
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, poste..."
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <select name="category" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                <option value="">Toutes catégories</option>
                <option value="administratif" {{ request('category') === 'administratif' ? 'selected':'' }}>Administratif</option>
                <option value="appoint"       {{ request('category') === 'appoint'       ? 'selected':'' }}>Appoint</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Personnel</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Poste</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Catégorie</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Salaire</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($staff as $member)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-sm font-bold">{{ strtoupper(substr($member->first_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $member->employee_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-600">{{ $member->position }}</td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $member->category === 'administratif' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($member->category) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-right font-semibold text-gray-800">
                        {{ number_format($member->base_salary, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $member->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $member->status === 'active' ? 'Actif' : ucfirst($member->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('staff.show', $member) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('staff.edit', $member) }}" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Modifier</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Aucun personnel trouvé</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($staff->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $staff->links() }}</div>
        @endif
    </div>
</div>
@endsection
