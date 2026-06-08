@extends('layouts.app')
@section('title', 'Nouveau paiement')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Enregistrer un paiement</h1>
            <p class="text-sm text-gray-400">Inscription ou mensualité</p>
        </div>
    </div>

    @if(!$currentYear)
    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm">Aucune année scolaire active. <a href="{{ route('school-years.create') }}" class="underline font-medium">Configurer →</a></span>
    </div>
    @endif

    <form method="POST" action="{{ route('payments.store') }}">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            {{-- Élève --}}
            <div class="p-6">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Élève</h2>
                <select name="student_id" id="student_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white"
                        onchange="onStudentChange(this)">
                    <option value="">-- Sélectionner un élève --</option>
                    @foreach($students as $s)
                    @php
                        $sInscriptionPaid = $currentYear
                            ? $s->payments->where('payment_type','inscription')->where('school_year_id',$currentYear->id)->whereIn('status',['paid','partial'])->isNotEmpty()
                            : false;
                    @endphp
                    <option value="{{ $s->id }}"
                            data-classroom="{{ $s->classroom_id }}"
                            data-year="{{ $s->school_year_id }}"
                            data-inscription-paid="{{ $sInscriptionPaid ? '1' : '0' }}"
                            {{ old('student_id', $student?->id) == $s->id ? 'selected' : '' }}>
                        {{ $s->full_name }} — {{ $s->classroom->name ?? '' }}
                    </option>
                    @endforeach
                </select>
                @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Type de paiement --}}
            <div class="p-6">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Type de paiement</h2>

                <div class="grid grid-cols-2 gap-3 mb-3" id="typeCards">
                    <label class="relative cursor-pointer" id="inscriptionCard">
                        <input type="radio" name="payment_type" value="inscription"
                               {{ old('payment_type', $defaultType) === 'inscription' ? 'checked' : '' }}
                               class="peer sr-only" onchange="onTypeChange('inscription')">
                        <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-4 text-center transition">
                            <div class="text-2xl mb-1">📋</div>
                            <p class="text-sm font-semibold text-gray-700">Inscription</p>
                            <p class="text-xs text-gray-400">Frais d'inscription annuels</p>
                            @if($currentYear)
                            <p class="text-xs font-bold text-indigo-600 mt-1">{{ number_format($currentYear->inscription_fee, 0, ',', ' ') }} F</p>
                            @endif
                        </div>
                    </label>

                    <label class="relative cursor-pointer" id="mensualiteCard">
                        <input type="radio" name="payment_type" value="mensualite"
                               {{ old('payment_type', $defaultType) === 'mensualite' ? 'checked' : '' }}
                               class="peer sr-only" onchange="onTypeChange('mensualite')">
                        <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-4 text-center transition">
                            <div class="text-2xl mb-1">📅</div>
                            <p class="text-sm font-semibold text-gray-700">Mensualité</p>
                            <p class="text-xs text-gray-400">Paiement mensuel</p>
                            @if($currentYear)
                            <p class="text-xs font-bold text-indigo-600 mt-1">{{ number_format($currentYear->monthly_fee, 0, ',', ' ') }} F</p>
                            @endif
                        </div>
                    </label>
                </div>

                {{-- Badge inscription déjà payée --}}
                <div id="inscriptionPaidBadge" class="{{ $inscriptionPaid ? '' : 'hidden' }} flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2.5 mb-3">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm text-emerald-700 font-medium">Inscription déjà réglée pour cette année scolaire</span>
                </div>

                {{-- Mois/Année --}}
                <div id="monthFields" class="{{ old('payment_type', $defaultType) === 'mensualite' ? '' : 'hidden' }} grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mois <span class="text-red-500">*</span></label>
                        <select name="month" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $i => $m)
                            <option value="{{ $i+1 }}" {{ old('month', now()->month) == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Année <span class="text-red-500">*</span></label>
                        <select name="year" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Champs cachés --}}
            <input type="hidden" name="classroom_id" id="classroom_id" value="{{ old('classroom_id', $student?->classroom_id) }}">
            <input type="hidden" name="school_year_id" id="school_year_id" value="{{ old('school_year_id', $student?->school_year_id ?? $currentYear?->id) }}">

            {{-- Montants --}}
            <div class="p-6">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Montants</h2>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Montant dû — lecture seule --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-sm font-medium text-gray-700">Montant dû (FCFA)</label>
                            <button type="button" onclick="unlockDue()" id="unlockBtn"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 hidden">
                                Modifier
                            </button>
                        </div>
                        <input type="number" name="amount_due" id="amount_due"
                               value="{{ old('amount_due', $currentYear?->inscription_fee ?? 0) }}"
                               min="0" step="1" required readonly
                               class="w-full border border-gray-100 bg-gray-50 rounded-xl px-4 py-2.5 text-sm outline-none font-semibold text-gray-700 cursor-not-allowed">
                        @error('amount_due')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Montant payé --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Montant payé (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount_paid" id="amount_paid"
                               value="{{ old('amount_paid', $currentYear?->inscription_fee ?? 0) }}"
                               min="0" step="1" required oninput="computeBalance()"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('amount_paid')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Résumé si paiement partiel --}}
                <div id="balanceBox" class="hidden mt-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-amber-800">Paiement partiel</span>
                        <span id="balanceStatus" class="text-xs font-medium text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Incomplet</span>
                    </div>
                    <div class="space-y-1 text-xs text-amber-700">
                        <div class="flex justify-between">
                            <span>Total dû</span>
                            <span id="dueDisplay" class="font-semibold">0 F</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Payé maintenant</span>
                            <span id="paidDisplay" class="font-semibold text-emerald-700">0 F</span>
                        </div>
                        <div class="flex justify-between border-t border-amber-200 pt-1.5 mt-1">
                            <span class="font-semibold">Reste à payer</span>
                            <span id="balanceAmount" class="font-bold text-red-600">0 F</span>
                        </div>
                    </div>
                    <div class="mt-2 h-1.5 bg-amber-200 rounded-full overflow-hidden">
                        <div id="progressBar" class="h-full bg-emerald-500 rounded-full transition-all" style="width:0%"></div>
                    </div>
                </div>
            </div>

            {{-- Détails --}}
            <div class="p-6">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Détails</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mode de paiement <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white">
                            <option value="cash"         {{ old('payment_method','cash') === 'cash' ? 'selected':'' }}>💵 Espèces</option>
                            <option value="virement"     {{ old('payment_method') === 'virement' ? 'selected':'' }}>🏦 Virement</option>
                            <option value="mobile_money" {{ old('payment_method') === 'mobile_money' ? 'selected':'' }}>📱 Mobile Money</option>
                            <option value="cheque"       {{ old('payment_method') === 'cheque' ? 'selected':'' }}>📄 Chèque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de paiement <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes (optionnel)</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none resize-none"
                              placeholder="Remarques...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer le paiement
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const FEES = {
    inscription: {{ $currentYear?->inscription_fee ?? 0 }},
    mensualite:  {{ $currentYear?->monthly_fee ?? 0 }},
};

function onTypeChange(type) {
    const due  = document.getElementById('amount_due');
    const paid = document.getElementById('amount_paid');
    due.value  = FEES[type] ?? 0;
    paid.value = due.value;
    document.getElementById('monthFields').classList.toggle('hidden', type !== 'mensualite');
    computeBalance();
}

function onStudentChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('classroom_id').value   = opt.dataset.classroom || '';
    document.getElementById('school_year_id').value = opt.dataset.year || '';

    const inscriptionPaid   = opt.dataset.inscriptionPaid === '1';
    const inscriptionCard   = document.getElementById('inscriptionCard');
    const badge             = document.getElementById('inscriptionPaidBadge');
    const inscriptionRadio  = inscriptionCard.querySelector('input[type="radio"]');
    const mensualiteRadio   = document.getElementById('mensualiteCard').querySelector('input[type="radio"]');

    if (inscriptionPaid) {
        inscriptionCard.classList.add('hidden');
        badge.classList.remove('hidden');
        document.getElementById('typeCards').classList.replace('grid-cols-2', 'grid-cols-1');
        mensualiteRadio.checked = true;
        onTypeChange('mensualite');
    } else {
        inscriptionCard.classList.remove('hidden');
        badge.classList.add('hidden');
        document.getElementById('typeCards').classList.replace('grid-cols-1', 'grid-cols-2');
        inscriptionRadio.checked = true;
        onTypeChange('inscription');
    }
}

function unlockDue() {
    const due = document.getElementById('amount_due');
    due.removeAttribute('readonly');
    due.classList.remove('bg-gray-50', 'cursor-not-allowed');
    due.classList.add('bg-white');
    document.getElementById('unlockBtn').classList.add('hidden');
    due.focus();
}

function computeBalance() {
    const due    = parseFloat(document.getElementById('amount_due').value) || 0;
    const paid   = parseFloat(document.getElementById('amount_paid').value) || 0;
    const box    = document.getElementById('balanceBox');

    if (paid <= 0 || paid >= due) {
        box.classList.add('hidden');
        return;
    }

    const balance = due - paid;
    const pct     = Math.min(100, Math.round((paid / due) * 100));

    document.getElementById('dueDisplay').textContent    = fmt(due);
    document.getElementById('paidDisplay').textContent   = fmt(paid);
    document.getElementById('balanceAmount').textContent = fmt(balance);
    document.getElementById('progressBar').style.width   = pct + '%';
    box.classList.remove('hidden');
}

function fmt(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' F';
}

document.addEventListener('DOMContentLoaded', function () {
    const type = document.querySelector('input[name="payment_type"]:checked')?.value || 'inscription';
    const due  = document.getElementById('amount_due');
    const paid = document.getElementById('amount_paid');

    if (!due.value || due.value === '0') due.value = FEES[type] ?? 0;
    if (!paid.value || paid.value === '0') paid.value = due.value;

    document.getElementById('unlockBtn').classList.remove('hidden');

    const studentSelect = document.getElementById('student_id');
    if (studentSelect.value) onStudentChange(studentSelect);
});
</script>
@endsection
