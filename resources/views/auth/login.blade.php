<!DOCTYPE html>
<html lang="fr" class="h-full bg-indigo-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">

<div class="w-full max-w-sm">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600 mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0-6l6.16-3.42M12 20.05a12.08 12.08 0 01-6.82-2.998"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ env('SCHOOL_NAME', config('app.name')) }}</h1>
        <p class="text-sm text-gray-500 mt-1">Connectez-vous à votre espace</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Connexion</h2>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       autocomplete="email" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input id="password" type="password" name="password"
                       autocomplete="current-password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none transition">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300">
                    <span>Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition text-sm mt-2">
                Se connecter
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        © {{ date('Y') }} {{ env('SCHOOL_NAME', config('app.name')) }}
    </p>
</div>

</body>
</html>
