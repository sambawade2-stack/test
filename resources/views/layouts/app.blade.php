<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ env('SCHOOL_NAME', config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased">

<div class="min-h-full flex flex-col">

    {{-- ─── Navbar ──────────────────────────────────────────────────────────── --}}
    <nav class="bg-indigo-700 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">

                {{-- Logo + liens --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
                        <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-sm hidden sm:block">
                            {{ env('SCHOOL_NAME', config('app.name')) }}
                        </span>
                    </a>

                    <div class="hidden md:flex items-center gap-1">

                        {{-- Tableau de bord --}}
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            Tableau de bord
                        </a>

                        {{-- Élèves --}}
                        <a href="{{ route('students.index') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('students.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            Élèves
                        </a>

                        {{-- Classes --}}
                        <a href="{{ route('classrooms.index') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('classrooms.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            Classes
                        </a>

                        {{-- Présences --}}
                        @if(auth()->user()->canAccess('attendance'))
                        <a href="{{ route('attendance.index') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('attendance.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            Présences
                        </a>
                        @endif

                        {{-- Scanner (profils autorisés) --}}
                        @if(in_array(auth()->user()->role, ['admin','directeur','comptable','surveillant','caissier']))
                        <a href="{{ route('scan.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('scan.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Scanner
                        </a>
                        @endif

                        {{-- Personnel dropdown --}}
                        @if(auth()->user()->canAccess('personnel_view'))
                        <div class="relative group">
                            <button class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition
                                {{ request()->routeIs('teachers.*') || request()->routeIs('staff.*') || request()->routeIs('payrolls.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                                Personnel
                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1 hidden group-hover:block z-50 w-48">
                                <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 overflow-hidden">
                                    <a href="{{ route('teachers.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Enseignants
                                    </a>
                                    <a href="{{ route('staff.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Personnel admin
                                    </a>
                                    <div class="border-t border-gray-50 my-1"></div>
                                    <a href="{{ route('payrolls.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Fiches de paie
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Paiements dropdown --}}
                        @if(auth()->user()->canAccess('payments'))
                        <div class="relative group">
                            <button class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition
                                {{ request()->routeIs('payments.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                                Paiements
                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute left-0 top-full pt-1 hidden group-hover:block z-50 w-52">
                                <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 overflow-hidden">
                                    <a href="{{ route('payments.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Tous les paiements
                                    </a>
                                    <a href="{{ route('payments.create') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Nouveau paiement
                                    </a>
                                    <div class="border-t border-gray-50 my-1"></div>
                                    <a href="{{ route('payments.unpaid') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Impayés du mois
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Finances --}}
                        @if(auth()->user()->canAccess('finance'))
                        <a href="{{ route('finance.index') }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('finance.*') ? 'bg-white/20 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
                            Finances
                        </a>
                        @endif

                    </div>
                </div>

                {{-- Utilisateur --}}
                <div class="flex items-center gap-3">
                    @auth
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <span class="text-indigo-100 text-sm">{{ explode(' ', auth()->user()->name)[0] }}</span>
                    </div>
                    @if(auth()->user()->canAccess('users'))
                    <a href="{{ route('users.index') }}"
                       class="text-indigo-200 hover:text-white transition {{ request()->routeIs('users.*') ? 'text-white' : '' }}"
                       title="Profils & utilisateurs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>
                    @endif
                    @if(auth()->user()->canAccess('settings'))
                    <a href="{{ route('settings.index') }}"
                       class="text-indigo-200 hover:text-white transition {{ request()->routeIs('settings.*') ? 'text-white' : '' }}"
                       title="Paramètres">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="text-indigo-200 hover:text-white text-sm transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden sm:inline">Déconnexion</span>
                        </button>
                    </form>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    {{-- ─── Contenu principal ───────────────────────────────────────────────── --}}
    <main class="flex-1">

        {{-- Alertes flash --}}
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="flex items-center justify-between bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <ul class="text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 py-3">
        <p class="text-center text-xs text-gray-300">
            {{ env('SCHOOL_NAME', config('app.name')) }} · Système de gestion scolaire
        </p>
    </footer>

</div>

</body>
</html>
