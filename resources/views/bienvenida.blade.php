{{--
    Pagina de inicio publica.
    Ruta: Route::get('/', fn () => view('bienvenida'))->name('inicio');

    El nombre del sistema esta en config/app.php (APP_NAME en el .env).

    Toda la portada es la hoja del cuaderno y sigue el tema elegido: hoja
    clara o pizarra. Las fotos van pegadas en la hoja como láminas, con su
    borde blanco, y no detrás del texto: así nada queda oscuro en el tema
    claro. La hoja de ejemplo del hero es siempre papel (.cuaderno).
--}}
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Cocina con lo que ya tienes</title>
    <meta name="description" content="Sistema que genera recetas a partir de los ingredientes disponibles en tu hogar, para aprovechar los alimentos y reducir el desperdicio.">

    <x-tema-inicial />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grano bg-masa-900 text-crema-100">

{{-- ═══════════════════════════════════════════════════════════════
     BARRA DE NAVEGACION
═══════════════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 border-b border-linea bg-masa-850/95 backdrop-blur-sm">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-6 py-3" aria-label="Principal">

        <a href="#" class="flex items-center gap-2.5">
            <span class="grid h-8 w-8 place-items-center rounded-full bg-oro-500 text-masa-900">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M8.1 13.34l2.83-2.83L3.91 3.5a4 4 0 000 5.66l4.19 4.18zm6.78-1.81a5.5 5.5 0 005.96-1.22 5.5 5.5 0 000-7.78l-7.78 7.78 1.82 1.22zM14.5 14.5l-1.41 1.41 5.09 5.09 1.41-1.41-5.09-5.09zM3.5 20.09L9.7 13.9l1.41 1.41-6.2 6.19-1.41-1.41z"/>
                </svg>
            </span>
            <span class="titulo-serif text-xl">{{ config('app.name') }}</span>
        </a>

        <div class="hidden items-center gap-1 md:flex">
            <a href="#como-funciona" class="rounded-md px-3.5 py-2 text-sm text-crema-300 transition hover:bg-oro-500/9 hover:text-crema-50">Cómo funciona</a>
            <a href="#capacidades"   class="rounded-md px-3.5 py-2 text-sm text-crema-300 transition hover:bg-oro-500/9 hover:text-crema-50">Capacidades</a>
            <a href="#proyecto"      class="rounded-md px-3.5 py-2 text-sm text-crema-300 transition hover:bg-oro-500/9 hover:text-crema-50">El proyecto</a>
        </div>

        <div class="flex items-center gap-1 sm:gap-2">
            <x-boton-tema />
            @auth
                <a href="{{ route('dashboard') }}" class="boton-oro px-4 py-2 text-sm">Ir al panel</a>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-md px-3.5 py-2 text-sm text-crema-300 transition hover:bg-oro-500/9 hover:text-crema-50 sm:inline-flex">Acceder</a>
                <a href="{{ route('registro') }}" class="boton-oro px-4 py-2 text-sm">Crear cuenta</a>
            @endauth
        </div>
    </nav>
</header>


{{-- ═══════════════════════════════════════════════════════════════
     HERO: el titular a la izquierda; a la derecha la foto pegada como
     lámina y, encima, una hoja del cuaderno que muestra el mecanismo.
═══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden border-b border-linea px-6 pb-24 pt-12 md:pl-12 lg:pt-24">

    {{-- La foto de fondo, con un velo del color del tema: denso detrás
         del texto, abierto a la derecha para que se vean los ingredientes --}}
    <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <img src="{{ asset('img/hero.jpg') }}" alt=""
             class="deriva-hero h-full w-full object-cover" fetchpriority="high">
        <div class="velo-tema absolute inset-0"></div>
    </div>

    <div class="mx-auto grid max-w-6xl items-center gap-14 lg:grid-cols-[1fr_1.05fr] lg:gap-12">

        <div class="text-center lg:text-left">
            <h1 class="titulo-serif text-[clamp(2.6rem,6.2vw,4.9rem)]">
                Cocina con lo que
                <span class="text-oro-400">ya tienes en casa</span>
            </h1>

            <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-crema-300 sm:text-lg lg:mx-0">
                Registra los ingredientes que tienes disponibles y el sistema te muestra
                qué puedes preparar hoy, qué te falta para cada receta y qué alimentos
                deberías usar antes de que se echen a perder.
            </p>

            <div class="mt-9 flex flex-col items-center gap-3 sm:flex-row sm:justify-center lg:justify-start">
                <a href="{{ route('registro') }}" class="boton-oro w-full sm:w-auto">
                    Comenzar ahora
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="#como-funciona" class="boton-vidrio w-full sm:w-auto">Ver cómo funciona</a>
            </div>

            <p class="mt-8 text-sm text-crema-500">
                Aprovechamiento de alimentos en el hogar &middot; Gestión 2026
            </p>
        </div>

        {{-- La hoja de ejemplo, apoyada sobre la foto de fondo. Los datos
             son ilustrativos y lo dicen. --}}
        <div class="relative mx-auto w-full max-w-sm">
            <figure class="cuaderno relative -rotate-2 border border-linea bg-masa-850 shadow-[0_30px_50px_-24px_rgb(0_0_0/0.55)]"
                    aria-label="Ejemplo de cómo funciona el sistema">
                <div class="hoja-cuadriculada con-margen-hoja px-5 pb-5 pt-4 sm:pl-14">
                    <div class="flex items-baseline justify-between gap-3 border-b border-linea-fuerte pb-2">
                        <p class="font-semibold text-crema-50">Mi despensa</p>
                        <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-crema-500">Ejemplo</p>
                    </div>

                    <ul class="text-sm">
                        @foreach ([
                            ['Papa', '2 kg', false],
                            ['Maní', '250 g', false],
                            ['Fideo', '400 g', true],
                            ['Carne de res', '500 g', false],
                        ] as [$nombre, $cantidad, $urgente])
                            <li class="flex h-8 items-center justify-between gap-3 border-b border-linea">
                                <span class="text-crema-100">{{ $nombre }}</span>
                                <span class="flex items-center gap-2 tabular-nums text-crema-300">
                                    @if ($urgente)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-alerta-400">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 110 20 10 10 0 010-20zm1 5h-2v6l5 3 1-1.7-4-2.3V7z"/></svg>
                                            vence pronto
                                        </span>
                                    @endif
                                    {{ $cantidad }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex items-baseline justify-between gap-3 border-b border-linea-fuerte pb-2">
                        <p class="font-semibold text-crema-50">Puedes cocinar hoy</p>
                        <p class="titulo-serif text-xl tabular-nums text-oro-500">92%</p>
                    </div>
                    <p class="titulo-serif mt-2 text-2xl">Sopa de maní</p>
                    <p class="mt-1 text-xs text-crema-300">Usa el fideo que vence pronto &middot; te falta: arveja</p>
                    <p class="mt-3">
                        <span class="sello-verificada sello-estampar"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z"/></svg>Verificada</span>
                    </p>
                </div>
            </figure>
        </div>
    </div>

    {{-- Las dos cifras del problema, dichas como frase y con su fuente --}}
    <div class="mx-auto mt-20 max-w-6xl border-t border-linea-fuerte pt-6">
        <p class="max-w-3xl text-lg leading-relaxed text-crema-100 sm:text-xl">
            Un tercio de los alimentos producidos en el mundo se pierde o desperdicia,
            y el <strong class="font-bold text-crema-50">61%</strong> de ese desperdicio
            ocurre dentro de los hogares.
        </p>
        <p class="mt-3 text-xs leading-relaxed text-crema-500">
            Cifras de la Organización de las Naciones Unidas para la Alimentación y la Agricultura (FAO)
            y del Programa de las Naciones Unidas para el Medio Ambiente (PNUMA).
        </p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════
     COMO FUNCIONA: una lista renglonada y numerada; el número sí dice
     algo, porque el orden es justamente lo que se explica.
═══════════════════════════════════════════════════════════════ --}}
<section id="como-funciona" class="hoja-cuadriculada con-margen border-b border-linea px-6 py-24 md:pl-12">
    <div class="mx-auto max-w-6xl">

        <h2 class="titulo-serif max-w-2xl text-[clamp(2.2rem,5vw,3.6rem)]">
            Tres pasos, sin complicaciones
        </h2>

        <ol class="mt-12 border-t border-linea-fuerte">
            @foreach ([
                ['1', 'Registra tu despensa',
                 'Anota lo que tienes en casa con su cantidad, unidad de medida y fecha de caducidad. El sistema entiende gramos, kilos, tazas, cucharadas y piezas, y convierte entre ellas por ti.'],
                ['2', 'Genera recetas',
                 'El sistema compara tu inventario contra cada receta y calcula un porcentaje de compatibilidad. Te muestra primero las que mejor aprovechan lo que ya tienes.'],
                ['3', 'Cocina y aprovecha',
                 'Sigue los pasos de preparación, revisa qué ingredientes te faltaron, guarda tus favoritas y deja tu valoración para que otros la encuentren.'],
            ] as [$numero, $titulo, $cuerpo])
                <li class="grid gap-x-8 gap-y-2 border-b border-linea-fuerte py-8 sm:grid-cols-[4.5rem_minmax(0,18rem)_minmax(0,1fr)] sm:items-baseline">
                    <span class="titulo-serif text-5xl tabular-nums text-oro-500" aria-hidden="true">{{ $numero }}</span>
                    <h3 class="titulo-serif text-2xl">
                        <span class="sr-only">Paso {{ $numero }}: </span>{{ $titulo }}
                    </h3>
                    <p class="max-w-prose text-sm leading-relaxed text-crema-300 sm:text-base">{{ $cuerpo }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════
     CAPACIDADES: la lista en dos columnas y la segunda foto pegada
═══════════════════════════════════════════════════════════════ --}}
<section id="capacidades" class="relative overflow-hidden border-b border-linea px-6 py-24 md:pl-12">

    {{-- La segunda foto de fondo; aquí el velo es parejo porque la lista
         ocupa todo el ancho --}}
    <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <img src="{{ asset('img/capacidades.jpg') }}" alt="" loading="lazy"
             class="deriva-seccion h-full w-full object-cover">
        <div class="absolute inset-0 bg-masa-900/85"></div>
    </div>

    <div class="mx-auto max-w-6xl">

        <h2 class="titulo-serif max-w-2xl text-[clamp(2.2rem,5vw,3.6rem)]">
            Todo lo que hace el sistema
        </h2>

        <ul class="mt-12 grid gap-x-12 md:grid-cols-2">
            @foreach ([
                [
                    'titulo' => 'Coincidencia de ingredientes',
                    'cuerpo' => 'Compara lo que tienes contra lo que pide cada receta, convirtiendo las unidades antes de decidir. Un kilo de carne cubre los 500 gramos de la receta; 100 mililitros de arroz no cubren una taza.',
                    'tags'   => ['Conversión automática', 'Porcentaje de compatibilidad', 'Ingredientes faltantes'],
                    'icono'  => 'M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z',
                ],
                [
                    'titulo' => 'Inventario y caducidades',
                    'cuerpo' => 'Tu despensa siempre a la vista, con alertas de lo que está por vencer. El sistema prioriza las recetas que usan esos alimentos primero, para que nada termine en la basura.',
                    'tags'   => ['Alertas de vencimiento', 'Control de cantidades', 'Prioridad de uso'],
                    'icono'  => 'M12 2a10 10 0 100 20 10 10 0 000-20zm1 10.6V6h-2v7.4l5.2 3.1 1-1.7-4.2-2.2z',
                ],
                [
                    'titulo' => 'Recetas asistidas por IA',
                    'cuerpo' => 'Cuando ninguna receta guardada encaja del todo, el sistema propone una nueva a partir de tus ingredientes. Un nutricionista la revisa antes de que quede marcada como verificada.',
                    'tags'   => ['Generación estructurada', 'Revisión profesional', 'Recetas verificadas'],
                    'icono'  => 'M9 2v6.5L4.5 17A3 3 0 007.2 21.5h9.6A3 3 0 0019.5 17L15 8.5V2H9zm2 2h2v5l4.2 8a1 1 0 01-.9 1.5H7.7a1 1 0 01-.9-1.5L11 9V4z',
                ],
                [
                    'titulo' => 'Historial y favoritas',
                    'cuerpo' => 'Cada búsqueda queda guardada con los resultados que obtuviste, para volver a ellos cuando quieras. Las recetas que más te gustan se guardan aparte, a un clic.',
                    'tags'   => ['Consultas anteriores', 'Recetas guardadas', 'Valoraciones'],
                    'icono'  => 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
                ],
                [
                    'titulo' => 'Validación nutricional',
                    'cuerpo' => 'El nutricionista revisa ingredientes, pasos e información nutricional antes de aprobar una receta. Solo las aprobadas pueden marcarse como verificadas.',
                    'tags'   => ['Aprobación por rol', 'Observaciones', 'Información nutricional'],
                    'icono'  => 'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1.2 14.4L7 11.6l1.4-1.4 2.4 2.4 4.8-4.8L17 9.2l-6.2 6.2z',
                ],
                [
                    'titulo' => 'Reportes y monitoreo',
                    'cuerpo' => 'El administrador ve usuarios registrados, recetas almacenadas, consultas realizadas y valoraciones. Cada acción relevante queda registrada en la bitácora del sistema.',
                    'tags'   => ['Informes', 'Bitácora de acciones', 'Control de accesos'],
                    'icono'  => 'M5 9.2h3V19H5zM10.6 5H14v14h-3.4zM16.2 13H19v6h-2.8z',
                ],
            ] as $capacidad)
                <li class="flex gap-4 border-t border-linea-fuerte py-7">
                    <svg viewBox="0 0 24 24" class="mt-1 h-5 w-5 shrink-0 text-oro-500" fill="currentColor" aria-hidden="true">
                        <path d="{{ $capacidad['icono'] }}"/>
                    </svg>
                    <div class="min-w-0">
                        <h3 class="titulo-serif text-2xl">{{ $capacidad['titulo'] }}</h3>
                        <p class="mt-2.5 max-w-prose text-sm leading-relaxed text-crema-300">{{ $capacidad['cuerpo'] }}</p>
                        <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-crema-500">
                            @foreach ($capacidad['tags'] as $tag)
                                <span>{{ $tag }}</span>
                            @endforeach
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════
     EL PROYECTO
═══════════════════════════════════════════════════════════════ --}}
<section id="proyecto" class="hoja-cuadriculada con-margen px-6 py-24 md:pl-12">
    <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-2 lg:gap-20">

        <div>
            <h2 class="titulo-serif text-[clamp(2.2rem,5vw,3.6rem)]">
                Un problema cotidiano
            </h2>

            <div class="mt-7 max-w-prose space-y-4 text-sm leading-relaxed text-crema-300 sm:text-base">
                <p>
                    En los hogares se compran alimentos que terminan olvidados al fondo
                    de la refrigeradora. No es por descuido: es que decidir qué cocinar
                    con lo que queda exige tiempo y creatividad que muchas veces no se tienen.
                </p>
                <p>
                    Este sistema invierte la pregunta. En lugar de partir de una receta
                    y salir a comprar lo que falta, parte de lo que ya está en casa y
                    propone qué se puede preparar con ello.
                </p>
            </div>

            <dl class="mt-9 grid grid-cols-2 gap-x-6 border-t border-linea-fuerte text-sm">
                @foreach ([
                    ['Metodología', 'ICONIX'],
                    ['Arquitectura', 'Modelo Vista Controlador'],
                    ['Base de datos', 'PostgreSQL'],
                    ['Alcance', 'La Paz, Bolivia'],
                ] as [$clave, $valor])
                    <div class="border-b border-linea py-3">
                        <dt class="text-xs font-semibold text-crema-500">{{ $clave }}</dt>
                        <dd class="mt-1 text-crema-100">{{ $valor }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- El video de comida (public/img/comida.mp4) encabeza el recuadro.
             Si no está, se usa una foto comida.jpg/.png/.webp; sin ninguna
             de las dos, el recuadro queda como está y no muestra un hueco. --}}
        @php
            $videoComida = is_file(public_path('img/comida.mp4')) ? 'img/comida.mp4' : null;
            $fotoComida  = $videoComida ? null : collect(['jpg', 'jpeg', 'png', 'webp'])
                ->map(fn ($ext) => "img/comida.{$ext}")
                ->first(fn ($ruta) => is_file(public_path($ruta)));
        @endphp

        <div class="vidrio flex flex-col justify-center rounded-xl">
            @if ($videoComida)
                {{-- Se reproduce solo, sin sonido y en bucle (muted es lo que
                     permite el autoplay en los navegadores) --}}
                <div class="border-b border-linea">
                    <video src="{{ asset($videoComida) }}" class="block aspect-video w-full object-cover"
                           autoplay muted loop playsinline preload="auto" aria-hidden="true"></video>
                </div>
            @elseif ($fotoComida)
                <img src="{{ asset($fotoComida) }}" alt="Un plato de comida casera"
                     loading="lazy" class="aspect-[16/9] w-full border-b border-linea object-cover">
            @endif

            <div class="p-8 sm:p-10">
            <h3 class="titulo-serif text-3xl sm:text-4xl">
                Tu primera receta está a un registro de distancia
            </h3>
            <p class="mt-4 text-sm leading-relaxed text-crema-300">
                Crear una cuenta toma menos de un minuto. Después solo tienes que
                anotar lo que hay en tu cocina.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('registro') }}" class="boton-oro w-full sm:w-auto">Crear mi cuenta</a>
                <a href="{{ route('login') }}" class="boton-vidrio w-full sm:w-auto">Ya tengo cuenta</a>
            </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════
     PIE DE PAGINA
═══════════════════════════════════════════════════════════════ --}}
<footer class="border-t border-linea bg-masa-850 px-6 py-10">
    <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-5 text-center sm:flex-row sm:text-left">

        <div class="flex items-center gap-2.5">
            <span class="grid h-7 w-7 place-items-center rounded-full bg-oro-500 text-masa-900">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor" aria-hidden="true">
                    <path d="M8.1 13.34l2.83-2.83L3.91 3.5a4 4 0 000 5.66l4.19 4.18zm6.78-1.81a5.5 5.5 0 005.96-1.22 5.5 5.5 0 000-7.78l-7.78 7.78 1.82 1.22zM14.5 14.5l-1.41 1.41 5.09 5.09 1.41-1.41-5.09-5.09zM3.5 20.09L9.7 13.9l1.41 1.41-6.2 6.19-1.41-1.41z"/>
                </svg>
            </span>
            <span class="titulo-serif text-lg">{{ config('app.name') }}</span>
        </div>

        <p class="text-xs leading-relaxed text-crema-500">
            Proyecto de grado &middot; Ingeniería de Sistemas &middot; Universidad Franz Tamayo<br>
            La Paz, Bolivia &middot; {{ date('Y') }}
        </p>

        <div class="flex gap-4 text-xs text-crema-300">
            <a href="{{ route('login') }}" class="transition hover:text-oro-400">Acceder</a>
            <a href="{{ route('registro') }}" class="transition hover:text-oro-400">Crear cuenta</a>
        </div>
    </div>
</footer>

</body>
</html>
