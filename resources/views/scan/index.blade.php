@extends('layouts.app')
@section('title', 'Scanner un badge')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Vérification de badge</h1>
            <p class="text-sm text-gray-400 mt-0.5">Scannez le QR code de l'élève pour vérifier s'il est en règle</p>
        </div>
        <a href="{{ route('scan.history') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-700 border border-gray-200 hover:border-indigo-300 px-4 py-2 rounded-xl font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Historique
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Scanner --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-800 text-sm">Caméra</h2>
            </div>
            <div class="p-5">
                <div id="reader" class="rounded-xl overflow-hidden bg-gray-900 aspect-square w-full"></div>
                <div class="flex gap-2 mt-3">
                    <button id="startScanBtn" onclick="startScan()"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold transition">
                        Démarrer le scan
                    </button>
                    <button id="stopScanBtn" onclick="stopScan()"
                            class="hidden flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition">
                        Arrêter
                    </button>
                </div>

                {{-- Saisie manuelle --}}
                <div class="mt-4 pt-4 border-t border-gray-50">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Ou saisir le matricule manuellement</label>
                    <div class="flex gap-2">
                        <input type="text" id="manualCode" placeholder="STU-2026-00001"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();verifyManual();}"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none uppercase">
                        <button onclick="verifyManual()"
                                class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-2 rounded-xl font-medium transition">
                            Vérifier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Résultat --}}
        <div id="resultPanel" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div id="resultIdle" class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-400">En attente d'un scan…</p>
            </div>
            <div id="resultContent" class="hidden"></div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let html5Qr = null;
let busy = false;
let lastCode = null;
let lastAt = 0;

function startScan() {
    document.getElementById('startScanBtn').classList.add('hidden');
    document.getElementById('stopScanBtn').classList.remove('hidden');

    getCtx(); // initialise l'audio sous le geste utilisateur (requis sur mobile)

    html5Qr = new Html5Qrcode('reader');
    html5Qr.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        onScanSuccess,
        () => {}
    ).catch(err => {
        alert("Impossible d'accéder à la caméra : " + err + "\nUtilisez la saisie manuelle.");
        stopScan();
    });
}

function stopScan() {
    document.getElementById('startScanBtn').classList.remove('hidden');
    document.getElementById('stopScanBtn').classList.add('hidden');
    if (html5Qr) {
        html5Qr.stop().then(() => html5Qr.clear()).catch(()=>{});
        html5Qr = null;
    }
}

function onScanSuccess(decodedText) {
    const now = Date.now();
    // Anti-doublon : ignore le même code rescanné dans les 3s
    if (decodedText === lastCode && (now - lastAt) < 3000) return;
    lastCode = decodedText;
    lastAt = now;
    verify(decodedText);
}

function verifyManual() {
    const code = document.getElementById('manualCode').value.trim();
    if (code) verify(code);
}

async function verify(code) {
    if (busy) return;
    busy = true;

    try {
        const res = await fetch('{{ route('scan.verify') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ code }),
        });
        const data = await res.json();
        renderResult(data);
    } catch (e) {
        renderError("Erreur de connexion. Réessayez.");
    } finally {
        setTimeout(() => busy = false, 800);
    }
}

// Échappe le HTML pour empêcher toute injection (XSS) via les données scannées
function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function renderResult(data) {
    clearTimeout(resetTimer);
    document.getElementById('resultIdle').classList.add('hidden');
    const panel = document.getElementById('resultContent');
    panel.classList.remove('hidden');

    if (!data.found) {
        panel.innerHTML = `
            <div class="bg-amber-50 px-5 py-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="font-bold text-amber-800">Élève introuvable</p>
                <p class="text-sm text-amber-600 mt-1">${esc(data.message ?? '')}</p>
            </div>`;
        finishScan(false);
        return;
    }

    const s  = data.student;
    const st = data.standing;
    const ok = st.in_order;

    const photo = s.photo
        ? `<img src="${esc(s.photo)}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow">`
        : `<div class="w-16 h-16 rounded-full bg-white/30 flex items-center justify-center"><span class="text-white text-2xl font-bold">${esc(s.name.charAt(0))}</span></div>`;

    const reasonsHtml = (st.reasons && st.reasons.length)
        ? `<ul class="mt-2 space-y-1">${st.reasons.map(r => `<li class="text-sm flex items-start gap-1.5"><span class="mt-1">•</span><span>${esc(r)}</span></li>`).join('')}</ul>`
        : '';

    panel.innerHTML = `
        <div class="${ok ? 'bg-emerald-500' : 'bg-red-500'} px-5 py-5 text-white">
            <div class="flex items-center gap-4">
                ${photo}
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-wide opacity-80">${ok ? 'En règle' : 'Pas en règle'}</p>
                    <p class="text-lg font-bold truncate">${esc(s.name)}</p>
                    <p class="text-sm opacity-90">${esc(s.classroom ?? '—')} · ${esc(s.matricule)}</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            <div class="flex items-center justify-center mb-4">
                ${ok
                    ? `<div class="flex items-center gap-2 text-emerald-600 font-bold text-lg"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> AUTORISÉ</div>`
                    : `<div class="flex items-center gap-2 text-red-600 font-bold text-lg"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> NON AUTORISÉ</div>`
                }
            </div>

            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="rounded-lg px-3 py-2 text-center ${st.inscription_paid ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'}">
                    <p class="text-xs font-medium">Inscription</p>
                    <p class="text-sm font-bold">${st.inscription_paid ? '✓ Payée' : '✗ Non payée'}</p>
                </div>
                <div class="rounded-lg px-3 py-2 text-center ${st.month_paid ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'}">
                    <p class="text-xs font-medium">Mensualité du mois</p>
                    <p class="text-sm font-bold">${st.month_paid ? '✓ Payée' : '✗ Non payée'}</p>
                </div>
            </div>

            ${!ok ? `<div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-red-700">${reasonsHtml}</div>` : ''}

            <div class="mt-4 flex gap-2">
                <a href="${s.profile_url}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-xl font-medium transition">Voir la fiche</a>
                ${!ok ? `<a href="{{ url('payments/create') }}?student_id=${s.id}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-xl font-medium transition">Encaisser</a>` : ''}
            </div>
        </div>`;

    finishScan(ok);
}

function renderError(msg) {
    clearTimeout(resetTimer);
    document.getElementById('resultIdle').classList.add('hidden');
    const panel = document.getElementById('resultContent');
    panel.classList.remove('hidden');
    panel.innerHTML = `<div class="px-5 py-8 text-center text-red-600">${msg}</div>`;
    finishScan(false);
}

// ─── Son + disparition auto du résultat ──────────────────────────────────────
let resetTimer = null;

function finishScan(success) {
    playTone(success);                       // son selon le résultat
    clearTimeout(resetTimer);
    resetTimer = setTimeout(resetResult, 3000); // efface après 3 s → badge suivant
}

function resetResult() {
    const panel = document.getElementById('resultContent');
    panel.classList.add('hidden');
    panel.innerHTML = '';
    document.getElementById('resultIdle').classList.remove('hidden');
    lastCode = null;   // autorise le rescan du même badge ensuite
}

// Contexte audio partagé (initialisé sous un geste utilisateur → fiable sur mobile)
let audioCtx = null;
function getCtx() {
    try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
    } catch (e) {}
    return audioCtx;
}

// Son : aigu (2 notes) = en règle, grave = refusé/introuvable
function playTone(success) {
    const ctx = getCtx();
    if (!ctx) return;
    try {
        if (success) {
            beepAt(ctx, 660, 0.0, 0.12, 'sine');
            beepAt(ctx, 990, 0.12, 0.18, 'sine');
        } else {
            beepAt(ctx, 200, 0.0, 0.45, 'square');
        }
    } catch (e) {}
}

function beepAt(ctx, freq, start, dur, type) {
    const o = ctx.createOscillator(); const g = ctx.createGain();
    o.connect(g); g.connect(ctx.destination);
    o.type = type; o.frequency.value = freq;
    g.gain.setValueAtTime(0.18, ctx.currentTime + start);
    o.start(ctx.currentTime + start);
    o.stop(ctx.currentTime + start + dur);
}
</script>
@endsection
