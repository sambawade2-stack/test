{{-- Partial formulaire utilisateur — $user peut être null (création) --}}
@php $current = $user->role ?? old('role', 'secretaire'); @endphp

<div class="p-6 space-y-5">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Sélection du profil (cards) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Profil / Rôle <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($roles as $key => $r)
            <label class="cursor-pointer">
                <input type="radio" name="role" value="{{ $key }}" class="peer sr-only"
                       {{ $current === $key ? 'checked' : '' }}>
                <div class="border-2 border-gray-200 peer-checked:border-{{ $r['color'] }}-500 peer-checked:bg-{{ $r['color'] }}-50 rounded-xl p-3 transition h-full">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $r['color'] }}-500"></span>
                        <span class="text-sm font-semibold text-gray-800">{{ $r['label'] }}</span>
                    </div>
                    <p class="text-xs text-gray-400 leading-tight">{{ $r['desc'] }}</p>
                </div>
            </label>
            @endforeach
        </div>
        @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Mot de passe --}}
    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-50">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Mot de passe
                @if(isset($user))
                <span class="text-xs font-normal text-gray-400">(laisser vide pour ne pas changer)</span>
                @else
                <span class="text-red-500">*</span>
                @endif
            </label>
            <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" {{ isset($user) ? '' : 'required' }}
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
        </div>
    </div>

</div>
