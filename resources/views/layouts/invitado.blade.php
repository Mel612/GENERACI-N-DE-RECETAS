{{-- Contenedor de las pantallas publicas de acceso: login y registro. --}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Acceso') &middot; {{ config('app.name') }}</title>


    <x-tema-inicial />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grano min-h-full bg-masa-900 text-crema-100">

{{-- La foto de la portada de fondo, en toda la pantalla. Se mueve despacio
     y sigue un poco al puntero (data-paralaje, en app.js). El velo es del
     color del tema y más denso en el centro, donde va el formulario. --}}
<div class="fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
    <img src="{{ asset('img/hero.jpg') }}" alt=""
         class="deriva-hero h-full w-full object-cover" data-paralaje>
    <div class="velo-acceso absolute inset-0"></div>
</div>

{{-- Regresar a la portada. Fijo arriba a la izquierda, siempre visible. --}}
<a href="{{ route('inicio') }}"
   class="boton-vidrio surgir fixed left-4 top-4 z-20 py-2.5 pl-3 pr-4 text-sm sm:left-6 sm:top-6">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M19 12H5M11 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="hidden sm:inline">Volver al inicio</span>
    <span class="sr-only sm:hidden">Volver al inicio</span>
</a>

<x-boton-tema class="fixed right-4 top-4 z-20 border border-linea bg-masa-850 sm:right-6 sm:top-6" />

<div class="flex min-h-screen flex-col items-center justify-center px-4 py-20 sm:px-6 sm:py-12">

    {{-- Marca --}}
    <a href="{{ route('inicio') }}"
       class="surgir mb-8 flex items-center gap-2.5 transition hover:opacity-80">
        <span class="grid h-9 w-9 place-items-center rounded-full bg-oro-500 text-masa-900">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                <path d="M8.1 13.34l2.83-2.83L3.91 3.5a4 4 0 000 5.66l4.19 4.18zm6.78-1.81a5.5 5.5 0 005.96-1.22 5.5 5.5 0 000-7.78l-7.78 7.78 1.82 1.22zM14.5 14.5l-1.41 1.41 5.09 5.09 1.41-1.41-5.09-5.09zM3.5 20.09L9.7 13.9l1.41 1.41-6.2 6.19-1.41-1.41z"/>
            </svg>
        </span>
        <span class="titulo-serif text-2xl">{{ config('app.name') }}</span>
    </a>

    <div class="surgir w-full @yield('ancho', 'max-w-md')" style="animation-delay:.1s">

        <div class="mb-7 text-center">
            <h1 class="titulo-serif text-[clamp(2rem,5vw,2.8rem)]">@yield('encabezado')</h1>
            <p class="mt-2 text-sm text-crema-300">@yield('subtitulo')</p>
        </div>

        {{-- La hoja del formulario apoyada sobre la foto --}}
        <div class="vidrio-solido rounded-xl p-7 shadow-[0_30px_60px_-30px_rgb(0_0_0/0.45)] sm:p-9">

            @if (session('exito'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-exito-400/25 bg-exito-400/10 px-4 py-3 text-sm text-exito-400">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1.3 14.3l-4-4 1.4-1.4 2.6 2.6 5.6-5.6L17.7 9l-7 7.3z"/>
                    </svg>
                    <span>{{ session('exito') }}</span>
                </div>
            @endif

            @yield('contenido')
        </div>

        {{-- Sobre la foto, la nota lleva su propio fondo para leerse --}}
        <p class="mx-auto mt-6 w-fit max-w-full rounded-md bg-masa-850/90 px-3 py-1.5 text-center text-xs text-crema-300">
            @yield('pie')
        </p>
    </div>
</div>

</body>
</html>
