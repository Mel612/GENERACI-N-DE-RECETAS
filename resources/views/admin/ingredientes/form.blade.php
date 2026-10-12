@extends('layouts.app')

@php $editando = $ingrediente->exists; @endphp

@section('titulo', $editando ? 'Editar ingrediente' : 'Nuevo ingrediente')

@section('contenido')
<x-cabecera :titulo="$editando ? 'Editar ingrediente' : 'Nuevo ingrediente'"
            subtitulo="Los valores nutricionales se registran por cada 100 gramos de alimento." />

<x-formulario :accion="$editando ? route('admin.ingredientes.update', $ingrediente) : route('admin.ingredientes.store')"
              :metodo="$editando ? 'PUT' : 'POST'"
              :volver="route('admin.ingredientes.index')"
              :textoEnviar="$editando ? 'Guardar cambios' : 'Registrar ingrediente'"
              archivos>

    <x-campo nombre="nom_ing" etiqueta="Nombre del ingrediente" :valor="$ingrediente->nom_ing" requerido
             placeholder="Papa" maxlength="60" pattern="\s*\p{L}[\p{L}\s,\-]*" title="Solo letras y espacios, sin números ni símbolos"
             data-sin-numeros
             ayuda="Solo letras y espacios, sin números ni símbolos." />

    <x-selector nombre="cod_categoria_ingrediente_ing"
                etiqueta="Categoría"
                :opciones="$categorias"
                clave="cod_categoria_ingrediente"
                texto="nom_cat"
                :valor="$ingrediente->cod_categoria_ingrediente_ing"
                requerido
                vacio="Elige una categoría" />

    <x-campo nombre="sin_ing" etiqueta="Sinónimos" :valor="$ingrediente->sin_ing"
             placeholder="patata, papa blanca" data-sin-numeros
             ayuda="Separados por coma. Sirven para que el buscador encuentre el ingrediente aunque lo escriban distinto." />

    {{-- La lógica (cálculo de calorías y botón de la IA) está en resources/js/ingredientes.js --}}
    <fieldset class="rounded-xl border border-oro-500/12 p-5" id="nutricion-ingrediente"
              data-url-ia="{{ app(\App\Services\AsistenteIA::class)->disponible() ? route('admin.ingredientes.nutricion-ia') : '' }}">
        <legend class="px-2 text-sm font-medium text-crema-300">Información nutricional por 100 g</legend>

        <div class="mb-5 flex flex-wrap items-center gap-3" data-solo-con-ia>
            <button type="button" class="boton-oro" data-completar-ia>
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                    <path d="M12 3l1.9 5.9H20l-4.9 3.6 1.9 5.9L12 14.8 7 18.4l1.9-5.9L4 8.9h6.1L12 3z"/>
                </svg>
                Completar automáticamente con IA
            </button>
            <p class="text-xs text-crema-500">Llena proteínas, carbohidratos, grasas, densidad y alérgeno a partir del nombre.</p>
        </div>

        <p data-aviso-ia role="status" aria-live="polite" class="mb-4 hidden"></p>

        <div class="grid gap-5 sm:grid-cols-2">
            <x-campo nombre="pro_ing" etiqueta="Proteínas (g)" tipo="number" step="0.01" min="0" max="100"
                     :valor="$ingrediente->pro_ing" placeholder="2" />
            <x-campo nombre="car_ing" etiqueta="Carbohidratos (g)" tipo="number" step="0.01" min="0" max="100"
                     :valor="$ingrediente->car_ing" placeholder="17" />
            <x-campo nombre="gra_ing" etiqueta="Grasas (g)" tipo="number" step="0.01" min="0" max="100"
                     :valor="$ingrediente->gra_ing" placeholder="0.1" />
            {{-- Sin name: el servidor la vuelve a calcular, no confía en lo que llegue --}}
            <div>
                <label for="cal_ing" class="etiqueta-campo">Calorías (kcal) <span class="font-normal text-crema-500">· automático</span></label>
                <input id="cal_ing" type="text" readonly tabindex="-1" class="campo cursor-not-allowed opacity-80"
                       value="{{ $ingrediente->cal_ing }}" placeholder="Se calcula sola">
                <p class="mt-1.5 text-xs text-crema-500">
                    4 × proteínas + 4 × carbohidratos + 9 × grasas
                </p>
            </div>
        </div>

        <p class="mt-4 text-xs text-crema-500">
            Puedes dejarlos vacíos y completarlos después, pero las recetas que usen este
            ingrediente no podrán calcular sus calorías por porción hasta que los registres.
        </p>
    </fieldset>

    <fieldset class="rounded-xl border border-oro-500/12 p-5">
        <legend class="px-2 text-sm font-medium text-crema-300">Conversión entre volumen y peso</legend>

        <x-campo nombre="den_ing" etiqueta="Densidad (gramos por mililitro)" tipo="number"
                 step="0.001" min="0.001" :valor="$ingrediente->den_ing" placeholder="0.850" />

        <div class="mt-4 space-y-2 text-xs leading-relaxed text-crema-500">
            <p>
                Sirve para que el sistema compare una receta que pide «2 tazas de arroz»
                contra una despensa que guarda «1 kg de arroz». Sin este dato no puede
                relacionarlos y descarta el ingrediente.
            </p>
            <p>
                Solo hace falta en alimentos que se miden de las dos formas: harinas,
                granos, azúcar, sal, especias y líquidos. Una cebolla o un huevo no lo necesitan.
            </p>
            <p class="text-crema-300">
                Referencias: agua 1,000 &middot; leche 1,030 &middot; aceite 0,920 &middot;
                arroz 0,850 &middot; azúcar 0,850 &middot; harina 0,550 &middot;
                sal 1,200 &middot; especias molidas 0,450 &middot; hierbas picadas 0,250
            </p>
        </div>
    </fieldset>

    {{-- Con el precio, la pantalla de Aprovechamiento dice cuánto dinero se desperdicia --}}
    <x-campo nombre="pre_ing" etiqueta="Precio de referencia (opcional)" tipo="number"
             step="0.01" min="0.01" :valor="$ingrediente->pre_ing" placeholder="12.50"
             ayuda="Bs por kilo, referencia" />

    <x-campo-imagen nombre="img_ing" etiqueta="Fotografía del ingrediente"
                    :url="$ingrediente->urlImagen()" />

    <div class="space-y-3 border-t border-oro-500/10 pt-5">
        <x-interruptor nombre="ale_ing" etiqueta="Es un alérgeno común" :valor="$ingrediente->ale_ing ?? false"
                       ayuda="Se marcará en las recetas para que los usuarios con alergias lo vean." />

        <x-interruptor nombre="act_ing" etiqueta="Ingrediente activo" :valor="$ingrediente->act_ing ?? true"
                       ayuda="Los inactivos no aparecen al armar una despensa ni una receta." />
    </div>
</x-formulario>
@endsection
