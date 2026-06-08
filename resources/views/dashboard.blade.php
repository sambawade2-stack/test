@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')

{{-- ─── Hero banner ──────────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-6 mb-6 shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <circle cx="350" cy="30" r="120" fill="white"/>
            <circle cx="50" cy="180" r="80" fill="white"/>
        </svg>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <p class="text-indigo-200 text-sm font-medium mb-1">
                {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </p>
            <h1 class="text-2xl font-bold text-white">
                Bonjour, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p class="text-indigo-200 text-sm mt-1">
                @if($currentYear)
                    Année scolaire
                    <span class="bg-white/20 text-white px-2 py-0.5 rounded-full text-xs font-semibold ml-1">
                        {{ $currentYear->name }}
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('payments.create') }}"
           class="hidden sm:inline-flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 px-5 py-2.5 rounded-xl font-semibold text-sm shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau paiement
        </a>
    </div>
</div>

{{-- ─── KPI cards ────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                +{{ $stats['total_classrooms'] }} classes
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
        <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">Élèves actifs</p>
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 mt-3 font-medium">
            Voir la liste <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Ce mois</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['monthly_collected'], 0, ',', ' ') }}</p>
        <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">FCFA encaissés</p>
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-800 mt-3 font-medium">
            Voir paiements <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group {{ $stats['unpaid_this_month'] > 0 ? 'ring-1 ring-red-100' : '' }}">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl {{ $stats['unpaid_this_month'] > 0 ? 'bg-red-50' : 'bg-gray-50' }} flex items-center justify-center transition">
                <svg class="w-5 h-5 {{ $stats['unpaid_this_month'] > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            @if($stats['unpaid_this_month'] > 0)
            <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full animate-pulse">Attention</span>
            @else
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">OK</span>
            @endif
        </div>
        <p class="text-3xl font-bold {{ $stats['unpaid_this_month'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
            {{ number_format($stats['unpaid_this_month']) }}
        </p>
        <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">Impayés ce mois</p>
        <a href="{{ route('payments.unpaid') }}" class="inline-flex items-center gap-1 text-xs {{ $stats['unpaid_this_month'] > 0 ? 'text-red-600 hover:text-red-800' : 'text-gray-400' }} mt-3 font-medium">
            Voir les impayés <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center group-hover:bg-violet-100 transition">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-0.5 rounded-full">
                +{{ $stats['total_staff'] }} admin
            </span>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_teachers']) }}</p>
        <p class="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wide">Enseignants actifs</p>
        <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-1 text-xs text-violet-600 hover:text-violet-800 mt-3 font-medium">
            Voir le personnel <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

</div>

{{-- ─── Actions rapides / Paie / Modules ────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    {{-- Accès rapides --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-4 text-sm">Actions rapides</h3>
        <div class="grid grid-cols-2 gap-2">
            @if(auth()->user()->canAccess('students_manage'))
            <a href="{{ route('students.create') }}"
               class="flex flex-col items-center gap-2 p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 transition">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-indigo-700 text-center">Nouvel élève</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('payments'))
            <a href="{{ route('payments.create') }}"
               class="flex flex-col items-center gap-2 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-700 text-center">Encaisser</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('personnel_manage'))
            <a href="{{ route('teachers.create') }}"
               class="flex flex-col items-center gap-2 p-3 rounded-xl bg-violet-50 hover:bg-violet-100 transition">
                <div class="w-8 h-8 bg-violet-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-violet-700 text-center">Enseignant</span>
            </a>
            @endif
            @if(auth()->user()->canAccess('payrolls'))
            <a href="{{ route('payrolls.create') }}"
               class="flex flex-col items-center gap-2 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-amber-700 text-center">Fiche paie</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Résumé paie --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-1 text-sm">Paie — {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</h3>
        <p class="text-xs text-gray-400 mb-4">État des fiches de paie du mois</p>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-500 font-medium">Payé</span>
                    <span class="font-bold text-emerald-600">{{ number_format($stats['payroll_total'], 0, ',', ' ') }} F</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full"
                         style="width: {{ $stats['payroll_total'] > 0 ? '70%' : '0%' }}"></div>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $stats['payroll_pending'] }}</p>
                    <p class="text-xs text-gray-400">En attente</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="text-center">
                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_teachers'] + $stats['total_staff'] }}</p>
                    <p class="text-xs text-gray-400">Total personnel</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <a href="{{ route('payrolls.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Gérer →</a>
            </div>
        </div>
    </div>

    {{-- Modules --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-4 text-sm">Modules</h3>
        <div class="space-y-1">
            @foreach([
                ['route' => 'students.index',    'label' => 'Gestion des élèves', 'count' => $stats['total_students']],
                ['route' => 'classrooms.index',  'label' => 'Classes',            'count' => $stats['total_classrooms']],
                ['route' => 'teachers.index',    'label' => 'Enseignants',        'count' => $stats['total_teachers']],
                ['route' => 'staff.index',       'label' => 'Personnel admin',    'count' => $stats['total_staff']],
                ['route' => 'payments.index',    'label' => 'Paiements',          'count' => null],
                ['route' => 'school-years.index','label' => 'Années scolaires',   'count' => null],
            ] as $mod)
            <a href="{{ route($mod['route']) }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-gray-50 transition group">
                <span class="text-sm text-gray-700 group-hover:text-gray-900 font-medium">{{ $mod['label'] }}</span>
                <div class="flex items-center gap-2">
                    @if($mod['count'] !== null)
                    <span class="text-xs font-bold text-gray-400">{{ $mod['count'] }}</span>
                    @endif
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>

{{-- ─── Paiements récents + Impayés ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Paiements récents (2/3) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <div>
                <h2 class="font-bold text-gray-800">Paiements récents</h2>
                <p class="text-xs text-gray-400 mt-0.5">Dernières transactions enregistrées</p>
            </div>
            <a href="{{ route('payments.index') }}"
               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 border border-indigo-100 hover:border-indigo-300 px-3 py-1.5 rounded-lg transition">
                Tout voir
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentPayments as $payment)
            <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50/50 transition">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br
                    {{ $payment->payment_type === 'inscription' ? 'from-indigo-400 to-indigo-600' : 'from-emerald-400 to-emerald-600' }}
                    flex items-center justify-center shrink-0">
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(substr($payment->student->first_name ?? '?', 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $payment->student->full_name ?? '—' }}</p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $payment->classroom->name ?? '' }}
                        @if($payment->payment_type === 'mensualite')
                        · {{ $payment->month_label }} {{ $payment->year }}
                        @else
                        · Inscription
                        @endif
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-gray-900">
                        {{ number_format($payment->amount_paid, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-400">F</span>
                    </p>
                    <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' :
                           ($payment->status === 'partial' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                        {{ $payment->status === 'paid' ? 'Payé' : ($payment->status === 'partial' ? 'Partiel' : 'Impayé') }}
                    </span>
                </div>
                <a href="{{ route('payments.show', $payment) }}" class="shrink-0 text-gray-300 hover:text-indigo-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-400">Aucun paiement enregistré</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Impayés ce mois (1/3) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 bg-gradient-to-r from-red-50 to-orange-50">
            <div class="flex items-center gap-2 mb-0.5">
                <div class="w-2 h-2 rounded-full bg-red-500 {{ $stats['unpaid_this_month'] > 0 ? 'animate-pulse' : '' }}"></div>
                <h2 class="font-bold text-gray-800 text-sm">Impayés du mois</h2>
            </div>
            <p class="text-xs text-gray-400">{{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</p>
        </div>
        <div class="overflow-y-auto max-h-72">
            @forelse($unpaidStudents as $student)
            <div class="flex items-center justify-between px-5 py-3 hover:bg-red-50/30 transition border-b border-gray-50 last:border-0">
                <div class="min-w-0 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <span class="text-red-600 text-xs font-bold">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $student->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $student->classroom->name ?? '—' }}</p>
                    </div>
                </div>
                <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                   class="shrink-0 ml-2 text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg transition font-medium">
                    Payer
                </a>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-emerald-700">Tout est à jour !</p>
                <p class="text-xs text-gray-400 mt-1">Aucun impayé ce mois</p>
            </div>
            @endforelse
        </div>
        @if($stats['unpaid_this_month'] > 5)
        <div class="px-5 py-3 border-t border-gray-50 bg-gray-50/50">
            <a href="{{ route('payments.unpaid') }}" class="text-xs text-red-600 hover:text-red-800 font-medium">
                Voir les {{ $stats['unpaid_this_month'] }} impayés →
            </a>
        </div>
        @endif
    </div>

</div>

@endsection
