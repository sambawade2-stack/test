@extends('layouts.app')
@section('title', 'Paiement ' . $payment->receipt_number)

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reçu N° {{ $payment->receipt_number }}</h1>
            <p class="text-sm text-gray-400">{{ $payment->payment_date->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
        </div>
        <div class="ml-auto flex gap-2">
            {{-- Bouton Payer le solde --}}
            @if($payment->balance > 0)
            <button onclick="document.getElementById('soldeModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Payer le solde
                <span class="bg-white/30 text-white text-xs px-1.5 py-0.5 rounded-full font-bold">
                    {{ number_format($payment->balance, 0, ',', ' ') }} F
                </span>
            </button>
            @endif

            <a href="{{ route('payments.receipt', $payment) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Statut banner --}}
        <div class="px-6 py-4
            {{ $payment->status === 'paid'    ? 'bg-emerald-50 border-b border-emerald-100' :
               ($payment->status === 'partial' ? 'bg-amber-50 border-b border-amber-100'   : 'bg-red-50 border-b border-red-100') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full
                        {{ $payment->status === 'paid' ? 'bg-emerald-100' : ($payment->status === 'partial' ? 'bg-amber-100' : 'bg-red-100') }}
                        flex items-center justify-center">
                        @if($payment->status === 'paid')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">
                            {{ $payment->status === 'paid' ? 'Paiement complet' : ($payment->status === 'partial' ? 'Paiement partiel' : 'Impayé') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ number_format($payment->amount_paid, 0, ',', ' ') }} F payés sur {{ number_format($payment->amount_due, 0, ',', ' ') }} F dus
                        </p>
                    </div>
                </div>

                {{-- Barre de progression --}}
                @php $pct = $payment->amount_due > 0 ? min(100, round(($payment->amount_paid / $payment->amount_due) * 100)) : 0 @endphp
                <div class="hidden sm:block w-32">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Réglé</span>
                        <span class="font-bold {{ $payment->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $payment->status === 'paid' ? 'bg-emerald-500' : 'bg-amber-400' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Infos élève --}}
        <div class="p-6 border-b border-gray-50">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Élève</h2>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($payment->student->first_name ?? '?', 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-bold text-gray-900">{{ $payment->student->full_name ?? '—' }}</p>
                    <p class="text-sm text-gray-400">{{ $payment->classroom->name ?? '' }} · {{ $payment->schoolYear->name ?? '' }}</p>
                    <p class="text-xs text-gray-400">Matricule : {{ $payment->student->registration_number ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Détails paiement --}}
        <div class="p-6">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Détails</h2>
            <dl class="space-y-3">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Type</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $payment->payment_type === 'inscription' ? 'Inscription' : 'Mensualité' }}
                        @if($payment->payment_type === 'mensualite') — {{ $payment->month_label }} {{ $payment->year }} @endif
                    </dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Mode de paiement</dt>
                    <dd class="font-medium text-gray-800">
                        @php $methods = ['cash'=>'Espèces','virement'=>'Virement','mobile_money'=>'Mobile Money','cheque'=>'Chèque'] @endphp
                        {{ $methods[$payment->payment_method] ?? $payment->payment_method }}
                    </dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Date</dt>
                    <dd class="font-medium text-gray-800">{{ $payment->payment_date->locale('fr')->isoFormat('D MMMM YYYY') }}</dd>
                </div>

                <div class="pt-3 border-t border-gray-50 space-y-2">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Montant dû</dt>
                        <dd class="font-bold text-gray-900">{{ number_format($payment->amount_due, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Déjà payé</dt>
                        <dd class="font-bold text-emerald-600">{{ number_format($payment->amount_paid, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @if($payment->balance > 0)
                    <div class="flex justify-between text-sm items-center">
                        <dt class="text-gray-500">Reste à payer</dt>
                        <dd class="font-bold text-red-500 text-base">{{ number_format($payment->balance, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @endif
                </div>

                @if($payment->notes)
                <div class="flex justify-between text-sm pt-3 border-t border-gray-50">
                    <dt class="text-gray-500">Notes</dt>
                    <dd class="text-gray-700">{{ $payment->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($payment->creator)
        <div class="px-6 py-3 bg-gray-50/50 border-t border-gray-50">
            <p class="text-xs text-gray-400">Enregistré par <span class="font-medium text-gray-600">{{ $payment->creator->name }}</span></p>
        </div>
        @endif
    </div>
</div>

{{-- ─── Modal : Payer le solde ──────────────────────────────────────────────── --}}
@if($payment->balance > 0)
<div id="soldeModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
     onclick="if(event.target===this) this.classList.add('hidden')">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

        {{-- Header modal --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="font-bold text-gray-900">Compléter le paiement</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $payment->student->full_name ?? '' }} · {{ $payment->receipt_number }}</p>
            </div>
            <button onclick="document.getElementById('soldeModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Résumé solde --}}
        <div class="px-6 py-4 bg-amber-50 border-b border-amber-100">
            <div class="flex items-center justify-between text-sm">
                <span class="text-amber-700">Montant total dû</span>
                <span class="font-bold text-gray-900">{{ number_format($payment->amount_due, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="text-amber-700">Déjà réglé</span>
                <span class="font-bold text-emerald-600">{{ number_format($payment->amount_paid, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-amber-200">
                <span class="text-sm font-semibold text-amber-800">Solde restant</span>
                <span class="text-lg font-bold text-red-600">{{ number_format($payment->balance, 0, ',', ' ') }} F</span>
            </div>
        </div>

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('payments.complement', $payment) }}">
            @csrf
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Montant à payer maintenant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="complement_amount"
                           id="complementAmount"
                           value="{{ $payment->balance }}"
                           min="1" max="{{ $payment->balance }}"
                           step="1" required
                           oninput="updateComplement(this.value)"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none text-lg font-semibold">
                    <div class="flex gap-2 mt-2">
                        <button type="button"
                                onclick="document.getElementById('complementAmount').value={{ $payment->balance }}; updateComplement({{ $payment->balance }})"
                                class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg font-medium transition">
                            Tout payer ({{ number_format($payment->balance, 0, ',', ' ') }} F)
                        </button>
                    </div>
                </div>

                <div id="newTotalBox" class="bg-gray-50 rounded-xl px-4 py-3 text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nouveau total payé</span>
                        <span id="newTotalPaid" class="font-bold text-emerald-600">
                            {{ number_format($payment->amount_paid + $payment->balance, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Solde après paiement</span>
                        <span id="newBalance" class="font-bold text-gray-900">0 F</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mode de paiement</label>
                        <select name="payment_method" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none bg-white">
                            <option value="cash">💵 Espèces</option>
                            <option value="virement">🏦 Virement</option>
                            <option value="mobile_money">📱 Mobile Money</option>
                            <option value="cheque">📄 Chèque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                        <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes (optionnel)</label>
                    <input type="text" name="notes" placeholder="Remarques..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none">
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-50 flex justify-between">
                <button type="button"
                        onclick="document.getElementById('soldeModal').classList.add('hidden')"
                        class="text-sm text-gray-500 hover:text-gray-700">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer le paiement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const alreadyPaid = {{ $payment->amount_paid }};
const totalDue    = {{ $payment->amount_due }};
const balance     = {{ $payment->balance }};

function updateComplement(value) {
    const amount    = parseFloat(value) || 0;
    const newPaid   = alreadyPaid + amount;
    const newBalance = Math.max(0, totalDue - newPaid);

    document.getElementById('newTotalPaid').textContent = fmt(newPaid);
    document.getElementById('newBalance').textContent   = fmt(newBalance);
    document.getElementById('newBalance').className     = newBalance === 0
        ? 'font-bold text-emerald-600'
        : 'font-bold text-amber-600';
}

function fmt(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' F';
}
</script>
@endif

@endsection
