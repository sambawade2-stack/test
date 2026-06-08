@props(['route', 'label'])

<a href="{{ route($route) }}"
   class="px-3 py-2 rounded text-sm font-medium transition
          {{ request()->routeIs(explode('.', $route)[0] . '.*') || request()->routeIs($route)
             ? 'bg-indigo-800 text-white'
             : 'text-indigo-100 hover:bg-indigo-600' }}">
    {{ $label }}
</a>
