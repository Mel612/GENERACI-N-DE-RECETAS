{{--
    Contenedor de las pantallas internas, ya con sesión iniciada.

    ── Por qué un menú lateral ──────────────────────────────────────────

    El sistema tiene dieciocho destinos. En la barra superior vivían
    metidos en desplegables, así que para saber que existía «Mi despensa»
    había que abrir un menú. En el lateral están todos a la vista, y cada
    rol ve solo los suyos: una cuenta de usuario ve cinco entradas, no
    dieciocho.

    En pantallas chicas el lateral se esconde y su contenido pasa a un
    <details> en la barra de arriba, que es el mismo patrón sin
    JavaScript que ya usábamos.
--}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') &middot; {{ config('app.name') }}</title>


    <x-tema-inicial />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grano min-h-full bg-masa-900 text-crema-100">

@php
    $usuario = auth()->user();
    $esAdmin = $usuario->esAdministrador();
    $esNutri = $usuario->esNutricionista();

    // Contador de la bandeja de validación: solo lo ve quien puede resolverla.
    $porRevisar = ($esAdmin || $esNutri)
        ? \App\Models\Receta::activas()->pendientesDeRevision()->count()
        : 0;

    /*
     * El menú entero, declarado en un sitio.
     *
     * Cada grupo dice qué roles lo ven. Agregar una pantalla es agregar
     * una línea aquí, no tocar el HTML en dos sitios como antes.
     *
     * 'coincide' es el patrón que decide si la entrada está activa; por
     * omisión es la propia ruta.
     */
    $menu = collect([
        [
            'titulo' => null,
            'roles'  => ['todos'],
            'items'  => [
                ['ruta' => 'dashboard', 'texto' => 'Inicio',
                 'icono' => 'M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z'],
            ],
        ],
        [
            'titulo' => 'Mi cocina',
            'roles'  => ['todos'],
            'items'  => [
                ['ruta' => 'inventario.index', 'texto' => 'Mi despensa', 'coincide' => 'inventario.*',
                 'icono' => 'M5 2h14a1 1 0 011 1v18a1 1 0 01-1 1H5a1 1 0 01-1-1V3a1 1 0 011-1zm2 3v5h4V5H7zm6 0v5h4V5h-4zm-6 9v5h4v-5H7zm6 0v5h4v-5h-4z'],
                ['ruta' => 'aprovechamiento.index', 'texto' => 'Aprovechamiento',
                 'icono' => 'M5 9.2h3V19H5V9.2zM10.6 5h2.8v14h-2.8V5zm5.6 8H19v6h-2.8v-6z'],
                ['ruta' => 'generador.index', 'texto' => '¿Qué cocino hoy?',
                 'icono' => 'M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z'],
                ['ruta' => 'recetas.index', 'texto' => 'Recetario',
                 'icono' => 'M18 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2zM8 6h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z'],
                ['ruta' => 'recetas.favoritas', 'texto' => 'Mis favoritas',
                 'icono' => 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'],
                ['ruta' => 'generador.historial', 'texto' => 'Mis búsquedas',
                 'icono' => 'M13 3a9 9 0 109 9h-2a7 7 0 11-7-7v3l4-4-4-4v3zm-1 5v5l4 2 .8-1.3-3.3-2V8H12z'],
            ],
        ],
        [
            'titulo' => 'Cocina',
            'roles'  => ['admin', 'nutri'],
            'items'  => [
                ['ruta' => 'admin.recetas.index', 'texto' => 'Recetas', 'coincide' => 'admin.recetas.*',
                 'icono' => 'M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h12v4H3v-4z'],
                ['ruta' => 'nutricionista.index', 'texto' => 'Validación', 'coincide' => 'nutricionista.*',
                 'contador' => $porRevisar,
                 'icono' => 'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z'],
            ],
        ],
        [
            'titulo' => 'Catálogos',
            'roles'  => ['admin', 'nutri'],
            'items'  => [
                ['ruta' => 'admin.ingredientes.index', 'texto' => 'Ingredientes',
                 'coincide' => 'admin.ingredientes.*', 'roles' => ['admin', 'nutri']],
                ['ruta' => 'admin.categorias-ingrediente.index', 'texto' => 'Categorías de ingrediente',
                 'coincide' => 'admin.categorias-ingrediente.*', 'roles' => ['admin']],
                ['ruta' => 'admin.unidades.index', 'texto' => 'Unidades de medida',
                 'coincide' => 'admin.unidades.*', 'roles' => ['admin']],
                ['ruta' => 'admin.categorias-receta.index', 'texto' => 'Categorías de receta',
                 'coincide' => 'admin.categorias-receta.*', 'roles' => ['admin']],
                ['ruta' => 'admin.etiquetas.index', 'texto' => 'Etiquetas',
                 'coincide' => 'admin.etiquetas.*', 'roles' => ['admin']],
                ['ruta' => 'admin.preferencias.index', 'texto' => 'Preferencias alimentarias',
                 'coincide' => 'admin.preferencias.*', 'roles' => ['admin']],
            ],
        ],
        [
            'titulo' => 'Administración',
            'roles'  => ['admin'],
            'items'  => [
                ['ruta' => 'usuarios.index', 'texto' => 'Usuarios', 'coincide' => 'usuarios.*'],
                ['ruta' => 'admin.roles.index', 'texto' => 'Roles y permisos', 'coincide' => 'admin.roles.*'],
                ['ruta' => 'admin.reportes.index', 'texto' => 'Reportes', 'coincide' => 'admin.reportes.*'],
                ['ruta' => 'admin.valoraciones.index', 'texto' => 'Valoraciones', 'coincide' => 'admin.valoraciones.*'],
                ['ruta' => 'admin.bitacora.index', 'texto' => 'Monitoreo', 'coincide' => 'admin.bitacora.*'],
            ],
        ],
    ]);

    // Un grupo o una entrada se ven si el rol de quien mira está en su lista
    $puedeVer = function (array $roles) use ($esAdmin, $esNutri) {
        return in_array('todos', $roles, true)
            || ($esAdmin && in_array('admin', $roles, true))
            || ($esNutri && in_array('nutri', $roles, true));
    };

    $menu = $menu
        ->filter(fn ($g) => $puedeVer($g['roles']))
        ->map(function ($g) use ($puedeVer) {
            $g['items'] = collect($g['items'])
                ->filter(fn ($i) => ! isset($i['roles']) || $puedeVer($i['roles']))
                ->values();
            return $g;
        })
        ->filter(fn ($g) => $g['items']->isNotEmpty())
        ->values();
@endphp

<div class="flex min-h-dvh">

    {{-- ══════════════════ Menú lateral (pantallas grandes) ══════════════════ --}}
    <aside class="hidden w-64 shrink-0 flex-col border-r border-linea bg-masa-850 lg:sticky lg:top-0 lg:flex lg:h-dvh">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-5 pb-5 pt-6">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-oro-500 text-masa-900">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M8.1 13.34l2.83-2.83L3.91 3.5a4 4 0 000 5.66l4.19 4.18zm6.78-1.81a5.5 5.5 0 005.96-1.22 5.5 5.5 0 000-7.78l-7.78 7.78 1.82 1.22zM14.5 14.5l-1.41 1.41 5.09 5.09 1.41-1.41-5.09-5.09zM3.5 20.09L9.7 13.9l1.41 1.41-6.2 6.19-1.41-1.41z"/>
                </svg>
            </span>
            <span class="titulo-serif text-2xl">{{ config('app.name') }}</span>
        </a>

        <nav class="flex flex-1 flex-col gap-5 overflow-y-auto px-3 pb-3" aria-label="Menú principal">
            @foreach ($menu as $grupo)
                <div>
                    @if ($grupo['titulo'])
                        <p class="lateral-grupo">{{ $grupo['titulo'] }}</p>
                    @endif

                    <div class="flex flex-col gap-px">
                        @foreach ($grupo['items'] as $item)
                            @php $activo = request()->routeIs($item['coincide'] ?? $item['ruta']); @endphp

                            <a href="{{ route($item['ruta']) }}" class="lateral-enlace"
                               @if ($activo) aria-current="page" @endif>
                                @isset ($item['icono'])
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="{{ $item['icono'] }}"/>
                                    </svg>
                                @endisset

                                <span class="flex-1 truncate">{{ $item['texto'] }}</span>

                                @if (($item['contador'] ?? 0) > 0)
                                    <span class="rounded-full bg-oro-500/25 px-2 py-0.5 text-[0.65rem] tabular-nums text-oro-300">
                                        {{ $item['contador'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="flex items-center gap-3 border-t border-linea px-4 py-3.5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-oro-500/16 text-sm font-semibold text-oro-400">
                {{ \Illuminate\Support\Str::of($usuario->nom_usu)->explode(' ')->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}
            </span>
            <span class="min-w-0">
                <span class="block truncate text-sm text-crema-100">{{ $usuario->nom_usu }}</span>
                <span class="block text-[0.68rem] uppercase tracking-wide text-oro-400">{{ $usuario->rol->nom_rol }}</span>
            </span>
        </div>
    </aside>

    {{-- ══════════════════ Contenido ══════════════════ --}}
    <div class="flex min-w-0 flex-1 flex-col">

        <header class="sticky top-0 z-40 border-b border-linea bg-masa-850/95 backdrop-blur-sm">
            <div class="flex items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

                {{-- En pantallas chicas, el mismo menú dentro de un <details> --}}
                <details class="menu relative lg:hidden">
                    <summary class="boton-vidrio cursor-pointer list-none px-4 py-2 text-sm">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                            <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/>
                        </svg>
                        Menú
                    </summary>

                    <div class="vidrio-solido absolute left-0 top-full z-50 mt-2 max-h-[70vh] w-72 overflow-y-auto rounded-2xl p-3">

                        {{-- En móvil el lateral no se ve, así que quién entró va aquí --}}
                        <div class="mb-3 flex items-center gap-3 border-b border-linea px-2 pb-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-oro-500/16 text-sm font-semibold text-oro-400">
                                {{ \Illuminate\Support\Str::of($usuario->nom_usu)->explode(' ')->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm text-crema-100">{{ $usuario->nom_usu }}</span>
                                <span class="block text-[0.68rem] uppercase tracking-wide text-oro-400">{{ $usuario->rol->nom_rol }}</span>
                            </span>
                        </div>

                        @foreach ($menu as $grupo)
                            <div class="mb-3 last:mb-0">
                                @if ($grupo['titulo'])
                                    <p class="lateral-grupo">{{ $grupo['titulo'] }}</p>
                                @endif

                                @foreach ($grupo['items'] as $item)
                                    <a href="{{ route($item['ruta']) }}" class="lateral-enlace"
                                       @if (request()->routeIs($item['coincide'] ?? $item['ruta'])) aria-current="page" @endif>
                                        <span class="flex-1 truncate">{{ $item['texto'] }}</span>
                                        @if (($item['contador'] ?? 0) > 0)
                                            <span class="rounded-full bg-oro-500/25 px-2 py-0.5 text-[0.65rem] tabular-nums text-oro-300">
                                                {{ $item['contador'] }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </details>

                {{-- Buscador: lleva al recetario --}}
                <form method="GET" action="{{ route('recetas.index') }}" role="search"
                      class="relative hidden max-w-md flex-1 sm:block">
                    <label for="buscar-global" class="sr-only">Buscar en el recetario</label>
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-crema-500">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                            <path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5z"/>
                        </svg>
                    </span>
                    <input id="buscar-global" type="search" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Buscar recetas o ingredientes..."
                           class="campo pl-11">
                </form>

                <div class="flex items-center gap-1 sm:gap-2">
                    <x-boton-tema />

                    {{-- En pantalla chica el lateral no se ve: el nombre va aquí --}}
                    <span class="mr-1 hidden text-right lg:hidden sm:block">
                        <span class="block text-sm leading-tight text-crema-100">{{ $usuario->nom_usu }}</span>
                        <span class="block text-[0.68rem] uppercase tracking-wide text-oro-400">{{ $usuario->rol->nom_rol }}</span>
                    </span>

                    <a href="{{ route('perfil.edit') }}"
                       @class([
                           'rounded-md px-3 py-2 text-sm transition hover:bg-oro-500/9 hover:text-crema-50',
                           'bg-oro-500/11 font-semibold text-oro-500' => request()->routeIs('perfil.*'),
                           'text-crema-300' => ! request()->routeIs('perfil.*'),
                       ])>Perfil</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-md px-3 py-2 text-sm text-crema-300 transition hover:bg-oro-500/9 hover:text-crema-50">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- La hoja: la cuadrícula arranca en la esquina del <main> y los
             rellenos son de 24 o 48 px, así el contenido calza en sus
             líneas. El margen rojo va a 24 px y el texto empieza a 48. --}}
        <main @class(['hoja-cuadriculada w-full flex-1 px-6 py-6 md:px-12 md:py-12',
                      'con-margen' => ! View::hasSection('sin-margen')])>

            @if (session('exito'))
                <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-exito-400/30 bg-exito-400/10 px-4 py-3 text-sm text-exito-400">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1.3 14.3l-4-4 1.4-1.4 2.6 2.6 5.6-5.6L17.7 9l-7 7.3z"/>
                    </svg>
                    <span>{{ session('exito') }}</span>
                </div>
            @endif

            @if (session('aviso'))
                <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-alerta-400/30 bg-alerta-400/10 px-4 py-3 text-sm text-alerta-400">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span>{{ session('aviso') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-xl border border-error-400/30 bg-error-400/10 px-4 py-3 text-sm text-error-400">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span aria-hidden="true">&middot;</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('contenido')
        </main>
    </div>
</div>

</body>
</html>
