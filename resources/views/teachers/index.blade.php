@extends('layouts.app')
@section('title', 'Enseignants')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Enseignants</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $teachers->total() }} enseignant(s)</p>
        </div>
        <a href="{{ route('teachers.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Ajouter
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, matière..."
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                <option value="">Tous</option>
                <option value="active" {{ request('status') === 'active' ? 'selected':'' }}>Actifs</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected':'' }}>Inactifs</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Filtrer</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Enseignant</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Matière</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Contact</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Salaire</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($teachers as $teacher)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-sm font-bold">{{ strtoupper(substr($teacher->first_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $teacher->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $teacher->employee_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-600">{{ $teacher->subject }}</td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-xs text-gray-500">
                        {{ $teacher->phone ?? '—' }}<br>{{ $teacher->email ?? '' }}
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-right font-semibold text-gray-800">
                        {{ number_format($teacher->base_salary, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $teacher->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $teacher->status === 'active' ? 'Actif' : ucfirst($teacher->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('teachers.show', $teacher) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Voir</a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="text-xs text-gray-500 hover:text-gray-700 font-medium">Modifier</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Aucun enseignant trouvé</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($teachers->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $teachers->links() }}</div>
        @endif
    </div>
</div>
@endsection
