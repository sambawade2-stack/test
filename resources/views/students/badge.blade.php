@extends('layouts.app')
@section('title', 'Badge — ' . $student->full_name)

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('students.show', $student) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Badge scolaire</h1>
            <p class="text-sm text-gray-400">Prévisualisation avant impression</p>
        </div>
        <a href="{{ route('students.badge', $student) }}?download=1"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger le PDF
        </a>
    </div>

    {{-- Carte badge (aperçu agrandi) --}}
    <div class="flex justify-center py-6">
        <div class="bg-white rounded-2xl shadow-xl border border-indigo-100 overflow-hidden"
             style="width: 420px;">

            {{-- Bandeau --}}
            <div class="bg-indigo-600 text-white px-5 py-3">
                <p class="font-bold uppercase tracking-wide text-sm">{{ $school['name'] }}</p>
                <p class="text-indigo-200 text-xs">Carte d'élève · {{ $student->schoolYear?->name ?? '' }}</p>
            </div>

            {{-- Corps --}}
            <div class="p-5">
                <div class="flex items-start gap-4">
                    {{-- Photo --}}
                    <div class="shrink-0">
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" alt=""
                                 class="w-20 h-24 rounded-lg object-cover border border-gray-200">
                        @else
                            <div class="w-20 h-24 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center">
                                <span class="text-4xl font-bold text-indigo-400">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Infos --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 text-lg leading-tight">{{ $student->full_name }}</p>
                        <p class="text-indigo-600 font-bold text-xs mb-2">{{ $student->registration_number }}</p>
                        <dl class="space-y-0.5 text-xs text-gray-600">
                            <div><span class="text-gray-400">Classe :</span> <span class="font-semibold text-gray-800">{{ $student->classroom?->name ?? '—' }}</span></div>
                            <div><span class="text-gray-400">Né(e) :</span> <span class="font-semibold text-gray-800">{{ $student->date_of_birth?->format('d/m/Y') }}</span></div>
                            <div><span class="text-gray-400">Tuteur :</span> <span class="font-semibold text-gray-800">{{ $student->parent_name ?? '—' }}</span></div>
                            <div><span class="text-gray-400">Tél :</span> <span class="font-semibold text-gray-800">{{ $student->parent_phone ?? '—' }}</span></div>
                        </dl>
                    </div>

                    {{-- QR --}}
                    <div class="shrink-0 text-center">
                        <img src="{{ $qr }}" alt="QR code" class="w-24 h-24">
                        <p class="text-[10px] text-gray-400 mt-1">Scannez-moi</p>
                    </div>
                </div>
            </div>

            {{-- Pied --}}
            <div class="border-t border-dashed border-gray-200 px-5 py-2 flex items-center justify-between">
                <span class="text-[11px] text-gray-400">{{ $school['phone'] ?? '' }}</span>
                <span class="text-[11px] font-bold text-indigo-600">{{ $student->schoolYear?->name ?? '' }}</span>
            </div>
        </div>
    </div>

    {{-- Info QR --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 flex items-start gap-2">
        <svg class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-indigo-700">
            Le QR code contient le matricule de l'élève. Scannez-le depuis le module
            <a href="{{ route('scan.index') }}" class="font-semibold underline">Vérification de badge</a>
            pour contrôler si l'élève est en règle.
        </p>
    </div>

</div>
@endsection
