@extends('layouts.app')
@section('title', 'Nouvelle fiche de paie')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('payrolls.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Nouvelle fiche de paie</h1>
    </div>

    <form method="POST" action="{{ route('payrolls.store') }}" id="payrollForm">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50">

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Personnel</h2>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="payable_type" value="teacher" class="peer sr-only"
                               {{ old('payable_type','teacher') === 'teacher' ? 'checked':'' }}
                               onchange="loadEmployees(this.value)">
                        <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-4 text-center transition">
                            <div class="text-2xl mb-1">👨‍🏫</div>
                            <p class="text-sm font-semibold">Enseignant</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payable_type" value="staff" class="peer sr-only"
                               {{ old('payable_type') === 'staff' ? 'checked':'' }}
                               onchange="loadEmployees(this.value)">
                        <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-4 text-center transition">
                            <div class="text-2xl mb-1">👤</div>
                            <p class="text-sm font-semibold">Personnel</p>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sélectionner <span class="text-red-500">*</span></label>
                    <select name="payable_id" id="payable_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none bg-white"
                            onchange="fillSalary(this)">
                        <option value="">-- Choisir --</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}" data-salary="{{ $t->base_salary }}" data-type="teacher"
                                {{ old('payable_id') == $t->id ? 'selected':'' }}>
                            {{ $t->full_name }} ({{ $t->subject }})
                        </option>
                        @endforeach
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" data-salary="{{ $s->base_salary }}" data-type="staff"
                                class="staff-option hidden {{ old('payable_id') == $s->id ? 'selected':'' }}"
                                style="display:none">
                            {{ $s->full_name }} ({{ $s->position }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Période & Montants</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Mois *</label>
                        <select name="month" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $i => $m)
                            <option value="{{ $i+1 }}" {{ old('month', now()->month) == $i+1 ? 'selected':'' }}>{{ $m }}</option>
                            @endforeach
                        </select></div>

                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Année *</label>
                        <select name="year" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            @for($y = now()->year; $y >= now()->year-2; $y--)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected':'' }}>{{ $y }}</option>
                            @endfor
                        </select></div>

                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Salaire de base (FCFA) *</label>
                        <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" required min="0" oninput="computeNet()"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>

                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Primes (FCFA)</label>
                        <input type="number" name="bonuses" id="bonuses" value="{{ old('bonuses', 0) }}" min="0" oninput="computeNet()"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>

                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Retenues (FCFA)</label>
                        <input type="number" name="deductions" id="deductions" value="{{ old('deductions', 0) }}" min="0" oninput="computeNet()"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3 flex flex-col justify-center">
                        <p class="text-xs text-indigo-500 mb-0.5">Net à payer</p>
                        <p id="netDisplay" class="text-xl font-bold text-indigo-700">0 F</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Paiement</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Mode *</label>
                        <select name="payment_method" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none bg-white">
                            <option value="virement"     {{ old('payment_method','virement') === 'virement' ? 'selected':'' }}>Virement</option>
                            <option value="cash"         {{ old('payment_method') === 'cash' ? 'selected':'' }}>Espèces</option>
                            <option value="mobile_money" {{ old('payment_method') === 'mobile_money' ? 'selected':'' }}>Mobile Money</option>
                            <option value="cheque"       {{ old('payment_method') === 'cheque' ? 'selected':'' }}>Chèque</option>
                        </select></div>

                    <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Date de paiement</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none"></div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-between bg-gray-50/50 rounded-b-2xl">
                <a href="{{ route('payrolls.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    Créer la fiche
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function computeNet() {
    const base = parseFloat(document.getElementById('base_salary').value) || 0;
    const bonuses = parseFloat(document.getElementById('bonuses').value) || 0;
    const deductions = parseFloat(document.getElementById('deductions').value) || 0;
    const net = base + bonuses - deductions;
    document.getElementById('netDisplay').textContent = new Intl.NumberFormat('fr-FR').format(net) + ' F';
}

function fillSalary(select) {
    const opt = select.options[select.selectedIndex];
    if (opt.dataset.salary) {
        document.getElementById('base_salary').value = opt.dataset.salary;
        computeNet();
    }
}

function loadEmployees(type) {
    const select = document.getElementById('payable_id');
    for (let opt of select.options) {
        if (opt.value === '') continue;
        if (type === 'teacher') {
            opt.style.display = opt.dataset.type === 'teacher' ? '' : 'none';
        } else {
            opt.style.display = opt.dataset.type === 'staff' ? '' : 'none';
        }
    }
    select.value = '';
}
</script>
@endsection
