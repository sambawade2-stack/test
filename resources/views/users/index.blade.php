@extends('layouts.app')
@section('title', 'Profils & utilisateurs')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Profils & utilisateurs</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $users->total() }} compte(s) · gérez les accès du personnel</p>
        </div>
        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            + Nouveau profil
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email..."
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <select name="role" class="border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                <option value="">Tous les profils</option>
                @foreach($roles as $key => $r)
                <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $r['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 text-white rounded-xl px-4 py-2 text-sm font-medium">Filtrer</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilisateur</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Contact</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Profil</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-{{ $u->role_color }}-400 to-{{ $u->role_color }}-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-sm font-bold">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $u->name }}
                                    @if($u->id === auth()->id())
                                    <span class="text-xs text-gray-400 font-normal">(vous)</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 md:hidden">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <p class="text-gray-600">{{ $u->email }}</p>
                        <p class="text-xs text-gray-400">{{ $u->phone ?? '—' }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex text-xs px-2.5 py-1 rounded-full font-medium bg-{{ $u->role_color }}-50 text-{{ $u->role_color }}-700">
                            {{ $u->role_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($u->is_active)
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-50 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Actif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Désactivé
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.edit', $u) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Modifier</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.toggle-active', $u) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs {{ $u->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium">
                                    {{ $u->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline"
                                  onsubmit="return confirm('Supprimer le profil de {{ $u->name }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Supprimer</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Aucun utilisateur trouvé</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Légende des profils --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Profils disponibles</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            @foreach($roles as $key => $r)
            <div class="flex items-center gap-3 p-2 rounded-lg">
                <span class="w-2.5 h-2.5 rounded-full bg-{{ $r['color'] }}-500 shrink-0"></span>
                <div>
                    <span class="text-sm font-medium text-gray-700">{{ $r['label'] }}</span>
                    <span class="text-xs text-gray-400"> — {{ $r['desc'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
