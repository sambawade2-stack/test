@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">← Retour</a>
            @if($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->full_name }}"
                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
            @else
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow">
                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">{{ $student->full_name }}</h1>
            <span class="text-xs px-2 py-1 rounded-full {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $student->is_active ? 'Actif' : 'Inactif' }}
            </span>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('students.badge', $student) }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Badge QR
            </a>
            @if(auth()->user()->canAccess('attendance'))
            <a href="{{ route('attendance.student', $student) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-sm px-4 py-2 rounded-lg transition">
                📋 Présences
            </a>
            @endif
            @if(auth()->user()->canAccess('certificates'))
            <a href="{{ route('students.certificate.scolarite', $student) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition">
                📄 Cert. Scolarité
            </a>
            @endif
            @if(auth()->user()->canAccess('students_manage'))
            <a href="{{ route('students.edit', $student) }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg transition">
                Modifier
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Infos personnelles --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Informations personnelles</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Matricule</p>
                    <p class="font-mono font-medium">{{ $student->registration_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Date de naissance</p>
                    <p class="font-medium">{{ $student->date_of_birth?->format('d/m/Y') }} ({{ $student->age }} ans)</p>
                </div>
                <div>
                    <p class="text-gray-500">Lieu de naissance</p>
                    <p class="font-medium">{{ $student->place_of_birth ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Genre</p>
                    <p class="font-medium">{{ $student->gender === 'M' ? 'Masculin' : 'Féminin' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Nationalité</p>
                    <p class="font-medium">{{ $student->nationality }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Classe</p>
                    <p class="font-medium">{{ $student->classroom?->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Année scolaire</p>
                    <p class="font-medium">{{ $student->schoolYear?->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Inscrit le</p>
                    <p class="font-medium">{{ $student->enrolled_at?->format('d/m/Y') }}</p>
                </div>
            </div>

            <hr class="my-4">
            <h3 class="font-medium text-gray-700 mb-3">Parent / Tuteur</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nom</p>
                    <p class="font-medium">{{ $student->parent_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Relation</p>
                    <p class="font-medium">{{ $student->parent_relation ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Téléphone</p>
                    <p class="font-medium">{{ $student->parent_phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium">{{ $student->parent_email ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Solde paiements --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Situation financière</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total payé</span>
                        <span class="font-bold text-green-600">{{ number_format($student->totalPaid(), 0, ',', ' ') }} F</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total dû</span>
                        <span class="font-bold text-gray-800">{{ number_format($student->totalDue(), 0, ',', ' ') }} F</span>
                    </div>
                    <div class="flex justify-between text-sm border-t pt-2">
                        <span class="text-gray-600">Solde restant</span>
                        <span class="font-bold {{ $student->outstandingBalance() > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($student->outstandingBalance(), 0, ',', ' ') }} F
                        </span>
                    </div>
                </div>
                <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                   class="mt-4 block text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition">
                    + Nouveau paiement
                </a>
            </div>
        </div>
    </div>

    {{-- Historique paiements --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-800">Historique des paiements</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">N° Reçu</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Période</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">Montant dû</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-600">Payé</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Statut</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($student->payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $payment->receipt_number }}</td>
                    <td class="px-4 py-3">{{ $payment->payment_type === 'inscription' ? 'Inscription' : 'Mensualité' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $payment->month_label }} {{ $payment->year }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($payment->amount_due, 0, ',', ' ') }} F</td>
                    <td class="px-4 py-3 text-right font-medium text-green-600">{{ number_format($payment->amount_paid, 0, ',', ' ') }} F</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs
                            {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : ($payment->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $payment->status === 'paid' ? 'Payé' : ($payment->status === 'partial' ? 'Partiel' : 'Impayé') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $payment->payment_date?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('payments.receipt', $payment) }}"
                           class="text-xs text-indigo-600 hover:underline">📄 Reçu</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-400">Aucun paiement enregistré.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
