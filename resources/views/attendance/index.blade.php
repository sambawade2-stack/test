@extends('layouts.app')
@section('title', 'Présences')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Feuille d'appel</h1>
            <p class="text-sm text-gray-400 mt-0.5">Saisie des présences par classe et par jour</p>
        </div>
    </div>

    {{-- Sélecteur classe + date --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Classe</label>
                <select name="classroom_id" onchange="this.form.submit()"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none bg-white">
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ $classroomId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm outline-none">
            </div>
            <div class="flex items-end">
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
        </div>
    </form>

    @if($students->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-12 text-center text-gray-400">
        Aucun élève actif dans cette classe.
    </div>
    @else
    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <input type="hidden" name="classroom_id" value="{{ $classroomId }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="period" value="journée">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Actions de masse --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 bg-gray-50/50">
                <span class="text-sm font-medium text-gray-600">{{ $students->count() }} élève(s)</span>
                <button type="button" onclick="markAll('present')"
                        class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-medium transition">
                    Tout marquer présent
                </button>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Élève</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Motif (si absent/retard)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($students as $student)
                    @php $cur = $existing[$student->id]->status ?? 'present'; @endphp
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $student->gender === 'F' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $student->registration_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @foreach(['present' => ['P','emerald'], 'absent' => ['A','red'], 'late' => ['R','amber'], 'excused' => ['E','sky']] as $val => $info)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status[{{ $student->id }}]" value="{{ $val }}"
                                           class="peer sr-only status-radio" data-student="{{ $student->id }}"
                                           {{ $cur === $val ? 'checked' : '' }}>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg border-2 border-gray-200 text-xs font-bold text-gray-500
                                                 peer-checked:border-{{ $info[1] }}-500 peer-checked:bg-{{ $info[1] }}-500 peer-checked:text-white transition"
                                          title="{{ ['present'=>'Présent','absent'=>'Absent','late'=>'Retard','excused'=>'Excusé'][$val] }}">
                                        {{ $info[0] }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <input type="text" name="reason[{{ $student->id }}]"
                                   value="{{ $existing[$student->id]->reason ?? '' }}"
                                   placeholder="—"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-indigo-300 outline-none">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 py-4 border-t border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500"></span>Présent</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-500"></span>Absent</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-500"></span>Retard</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-sky-500"></span>Excusé</span>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer l'appel
                </button>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
function markAll(status) {
    document.querySelectorAll('.status-radio').forEach(r => {
        if (r.value === status) r.checked = true;
    });
}
</script>
@endsection
