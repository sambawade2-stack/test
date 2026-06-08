@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Paramètres</h1>
        <p class="text-sm text-gray-400 mt-0.5">Informations de l'établissement et configuration générale</p>
    </div>

    {{-- Infos établissement --}}
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            <div class="px-6 py-4">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 text-sm">🏫</span>
                    Informations de l'établissement
                </h2>
            </div>

            <div class="p-6 grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de l'établissement</label>
                    <input type="text" name="school_name"
                           value="{{ old('school_name', config('school.name', env('SCHOOL_NAME'))) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"
                           placeholder="Ex: Collège/Lycée Sacré-Cœur">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                    <input type="text" name="school_phone"
                           value="{{ old('school_phone', env('SCHOOL_PHONE')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"
                           placeholder="+221 XX XXX XX XX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="school_email"
                           value="{{ old('school_email', env('SCHOOL_EMAIL')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                    <input type="text" name="school_address"
                           value="{{ old('school_address', env('SCHOOL_ADDRESS')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"
                           placeholder="Ex: Rue 10, Dakar, Sénégal">
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end bg-gray-50/50 rounded-b-2xl">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Enregistrer les informations
                </button>
            </div>
        </div>
    </form>

    {{-- Années scolaires --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 text-sm">📅</span>
                Années scolaires
            </h2>
            <a href="{{ route('school-years.create') }}"
               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 border border-indigo-100 hover:border-indigo-300 px-3 py-1.5 rounded-lg transition">
                + Nouvelle année
            </a>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Année</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Période</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Inscription</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mensualité</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($schoolYears as $year)
                <tr class="{{ $year->is_active ? 'bg-emerald-50/30' : '' }} hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($year->is_active)
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            @endif
                            <span class="font-bold text-gray-900">{{ $year->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-xs text-gray-500">
                        {{ $year->start_date->format('d/m/Y') }} → {{ $year->end_date->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                        {{ number_format($year->inscription_fee, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                        {{ number_format($year->monthly_fee, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($year->is_active)
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-100 text-emerald-700">Active</span>
                        @else
                        <form method="POST" action="{{ route('school-years.activate', $year) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 transition">
                                Activer
                            </button>
                        </form>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('school-years.edit', $year) }}" class="text-xs text-gray-500 hover:text-indigo-600 font-medium">Modifier</a>
                            @if($year->is_active)
                            <form method="POST" action="{{ route('school-years.close', $year) }}"
                                  onsubmit="return confirm('Fermer l\'année {{ $year->name }} ?')">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                                    Fermer
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune année scolaire</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tarifs de l'année active --}}
    @if($activeYear)
    <form method="POST" action="{{ route('settings.update-fees') }}">
        @csrf @method('PUT')
        <input type="hidden" name="school_year_id" value="{{ $activeYear->id }}">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">
            <div class="px-6 py-4">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 text-sm">💰</span>
                    Tarifs — Année {{ $activeYear->name }}
                    <span class="text-xs font-normal text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full ml-1">Active</span>
                </h2>
                <p class="text-xs text-gray-400 mt-1">Modification du tarif ne s'applique qu'aux nouveaux paiements</p>
            </div>

            <div class="p-6 grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Frais d'inscription (FCFA)</label>
                    <input type="number" name="inscription_fee"
                           value="{{ old('inscription_fee', $activeYear->inscription_fee) }}"
                           min="0" step="500" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mensualité (FCFA)</label>
                    <input type="number" name="monthly_fee"
                           value="{{ old('monthly_fee', $activeYear->monthly_fee) }}"
                           min="0" step="500" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end bg-gray-50/50 rounded-b-2xl">
                <button type="submit"
                        class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Mettre à jour les tarifs
                </button>
            </div>
        </div>
    </form>
    @endif

</div>
@endsection
