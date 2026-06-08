@extends('layouts.app')
@section('title', 'Finances')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Tableau de bord financier</h1>
            <p class="text-sm text-gray-400 mt-0.5">Recettes, encaissements et répartition</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.export.excel') }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-2 rounded-xl font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Excel
            </a>
            <a href="{{ route('finance.export.pdf') }}"
               class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-xl font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
        </div>
    </div>

    {{-- Indicateurs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Encaissé ce mois</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Encaissé cette année</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['this_year'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Impayés (solde dû)</p>
            <p class="text-2xl font-bold {{ $stats['outstanding'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($stats['outstanding'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">FCFA à recouvrer</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Net ce mois</p>
            <p class="text-2xl font-bold {{ $stats['net_month'] >= 0 ? 'text-indigo-600' : 'text-red-600' }}">{{ number_format($stats['net_month'], 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400 mt-1">Recettes − salaires payés</p>
        </div>
    </div>

    {{-- Graphique recettes mensuelles --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-bold text-gray-800">Recettes mensuelles</h2>
                <p class="text-xs text-gray-400">12 derniers mois</p>
            </div>
        </div>
        <div style="height: 300px;"><canvas id="monthlyChart"></canvas></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Répartition par type --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-bold text-gray-800 mb-4">Répartition inscription / mensualité</h2>
            <div style="height: 240px;"><canvas id="typeChart"></canvas></div>
        </div>

        {{-- Par mode de paiement --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="font-bold text-gray-800 mb-4">Par mode de paiement</h2>
            <div class="space-y-3 mt-6">
                @php $maxMethod = $byMethod->max() ?: 1; @endphp
                @forelse($byMethod as $method => $total)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $methodLabels[$method] ?? $method }}</span>
                        <span class="font-semibold text-gray-800">{{ number_format($total, 0, ',', ' ') }} F</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ ($total / $maxMethod) * 100 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">Aucun paiement enregistré</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top classes --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-800">Recettes par classe</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Classe</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Recettes</th>
                    <th class="px-5 py-3 w-1/2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php $maxClass = $byClass->max('total') ?: 1; @endphp
                @forelse($byClass as $row)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $row->cname }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ number_format($row->total, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3">
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($row->total / $maxClass) * 100 }}%"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400">Aucune donnée</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const fmt = v => new Intl.NumberFormat('fr-FR').format(v) + ' F';

// Recettes mensuelles (barres)
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Recettes',
            data: @json($chartData),
            backgroundColor: 'rgba(79, 70, 229, 0.8)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: c => fmt(c.parsed.y) } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => new Intl.NumberFormat('fr-FR', {notation:'compact'}).format(v) } },
            x: { grid: { display: false } }
        }
    }
});

// Répartition par type (doughnut)
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Inscription', 'Mensualité'],
        datasets: [{
            data: [{{ $byType['inscription'] ?? 0 }}, {{ $byType['mensualite'] ?? 0 }}],
            backgroundColor: ['rgba(99, 102, 241, 0.85)', 'rgba(16, 185, 129, 0.85)'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: c => c.label + ': ' + fmt(c.parsed) } }
        }
    }
});
</script>
@endsection
