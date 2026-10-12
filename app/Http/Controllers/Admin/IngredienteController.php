<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GestionaCatalogo;
use App\Http\Controllers\Concerns\GuardaImagenes;
use App\Http\Controllers\Concerns\ValidaNombres;
use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\CategoriaIngrediente;
use App\Models\Ingrediente;
use App\Services\AsistenteIA;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

/**
 * Catalogo general de ingredientes.
 *
 * Es el catalogo del que cada usuario arma su despensa, asi que es el mas
 * delicado de los seis: un ingrediente puede estar referenciado por recetas
 * y por inventarios de otras personas.
 *
 * Accesible para Administrador y Nutricionista, ambos con acceso completo.
 * Los valores nutricionales se expresan siempre por cada 100 gramos, para
 * que sean comparables entre ingredientes.
 */
class IngredienteController extends Controller
{
    use GuardaImagenes;

    use GestionaCatalogo;

    use ValidaNombres;

    public function index(Request $request): View
    {
        $consulta = Ingrediente::with('categoria')
            ->withCount(['recetas', 'inventarios']);

        $this->buscarEn($consulta, $request->string('buscar')->toString(), ['nom_ing', 'sin_ing']);

        if ($request->filled('categoria')) {
            $consulta->where('cod_categoria_ingrediente_ing', $request->integer('categoria'));
        }

        if ($request->filled('estado')) {
            $consulta->where('act_ing', $request->input('estado') === 'activos');
        }

        if ($request->boolean('sin_nutricion')) {
            $consulta->whereNull('cal_ing');
        }

        if ($request->boolean('sin_densidad')) {
            $consulta->whereNull('den_ing');
        }

        if ($request->boolean('sin_precio')) {
            $consulta->whereNull('pre_ing');
        }

        if ($request->boolean('alergenos')) {
            $consulta->where('ale_ing', true);
        }

        return view('admin.ingredientes.index', [
            'ingredientes'   => $consulta->orderBy('nom_ing')->paginate(20)->withQueryString(),
            'categorias'     => CategoriaIngrediente::where('act_cat', true)->orderBy('nom_cat')->get(),
            'sinNutricion'   => Ingrediente::whereNull('cal_ing')->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.ingredientes.form', [
            'ingrediente' => new Ingrediente(),
            'categorias'  => CategoriaIngrediente::where('act_cat', true)->orderBy('nom_cat')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // array_merge y no +: con + gana la clave de la izquierda, y datos()
        // trae en 'img_ing' el archivo subido, no la ruta donde se guardo.
        $ingrediente = Ingrediente::create(array_merge(
            $this->datos($request),
            ['img_ing' => $this->resolverImagen($request, 'img_ing', 'ingredientes')]
        ));

        Bitacora::registrar('crear', 'ingredientes', $ingrediente->cod_ingrediente, [
            'nombre' => $ingrediente->nom_ing,
        ]);

        return redirect()->route('admin.ingredientes.index')
                         ->with('exito', "Ingrediente «{$ingrediente->nom_ing}» registrado correctamente.");
    }

    public function edit(Ingrediente $ingrediente): View
    {
        return view('admin.ingredientes.form', [
            'ingrediente' => $ingrediente,
            'categorias'  => CategoriaIngrediente::where('act_cat', true)->orderBy('nom_cat')->get(),
        ]);
    }

    public function update(Request $request, Ingrediente $ingrediente): RedirectResponse
    {
        $ingrediente->update(array_merge(
            $this->datos($request, $ingrediente->cod_ingrediente),
            [
                'img_ing'     => $this->resolverImagen($request, 'img_ing', 'ingredientes', $ingrediente->img_ing),
                'fec_act_ing' => now(),
            ]
        ));

        Bitacora::registrar('editar', 'ingredientes', $ingrediente->cod_ingrediente);

        return redirect()->route('admin.ingredientes.index')
                         ->with('exito', 'Ingrediente actualizado correctamente.');
    }

    public function destroy(Ingrediente $ingrediente): RedirectResponse
    {
        [$tipo, $mensaje] = $this->eliminarODesactivar($ingrediente, ['recetas', 'inventarios'], 'act_ing');

        return redirect()->route('admin.ingredientes.index')->with($tipo, $mensaje);
    }

    /**
     * Valores nutricionales de un ingrediente según la IA, para rellenar el
     * formulario. No guarda nada: quien edita revisa y decide si los guarda.
     */
    public function nutricionIa(Request $request, AsistenteIA $asistente): JsonResponse
    {
        $datos = $request->validate([
            'nombre'    => ['required', 'string', 'max:150', 'regex:/^[^0-9]+$/u'],
            'categoria' => ['nullable', 'integer', 'exists:categorias_ingrediente,cod_categoria_ingrediente'],
        ], [
            'nombre.required' => 'Escribe primero el nombre del ingrediente.',
            'nombre.regex'    => 'El nombre del ingrediente no puede llevar números.',
        ]);

        try {
            $nutricion = $asistente->nutricionDe(
                Auth::user(),
                $datos['nombre'],
                CategoriaIngrediente::find($datos['categoria'] ?? null)?->nom_cat,
            );
        } catch (RuntimeException $e) {
            return response()->json(['mensaje' => $e->getMessage()], 422);
        }

        return response()->json($nutricion);
    }

    /**
     * Reglas de validacion y armado del arreglo a guardar.
     *
     * El nombre no puede repetirse en todo el catálogo (sin importar
     * mayúsculas, tildes ni categoría) ni coincidir con el sinónimo de otro
     * ingrediente: «patata» no puede ser un ingrediente aparte si ya es
     * sinónimo de «Papa». La IA revisa además que sea comestible.
     */
    private function datos(Request $request, ?int $id = null): array
    {
        $this->limpiarTextos($request, ['nom_ing']);

        // Sinónimos: en minúsculas, sin espacios de más ni repetidos
        $sinonimos = collect(explode(',', (string) $request->input('sin_ing')))
            ->map(fn ($s) => mb_strtolower(Str::squish($s)))
            ->filter()
            ->unique(fn ($s) => $this->normalizar($s))
            ->implode(', ');
        $request->merge(['sin_ing' => $sinonimos === '' ? null : $sinonimos]);

        $validador = Validator::make($request->all(), [
            'nom_ing' => [
                ...$this->reglasNombre(Ingrediente::class, 'nom_ing', $id, 'el ingrediente', minimo: 2),
                function (string $atributo, mixed $valor, \Closure $fallar) use ($id) {
                    $buscado = $this->normalizar((string) $valor);

                    $dueno = Ingrediente::query()
                        ->when($id, fn ($q) => $q->where('cod_ingrediente', '!=', $id))
                        ->whereNotNull('sin_ing')
                        ->get(['nom_ing', 'sin_ing'])
                        ->first(fn ($i) => in_array($buscado,
                            array_map(fn ($s) => $this->normalizar($s), explode(',', $i->sin_ing)), true));

                    if ($dueno) {
                        $fallar("«{$valor}» ya es sinónimo de «{$dueno->nom_ing}». Usa ese ingrediente en lugar de crear otro.");
                    }
                },
            ],
            'cod_categoria_ingrediente_ing' => ['required', 'integer', 'exists:categorias_ingrediente,cod_categoria_ingrediente'],
            'sin_ing' => ['nullable', 'string', 'max:300', 'regex:/^[\pL][\pL\s,\-]*$/u'],
            'pro_ing' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'car_ing' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'gra_ing' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'den_ing' => ['nullable', 'numeric', 'gt:0', 'max:9999.999'],
            'pre_ing' => ['nullable', 'numeric', 'gt:0', 'max:999999.99'],
            'img_ing' => self::IMAGEN_REGLAS,
        ], $this->mensajesNombre('nom_ing', 'el ingrediente') + [
            'sin_ing.regex'    => 'Los sinónimos solo pueden llevar letras, separadas por comas: nada de números ni símbolos.',
            'cod_categoria_ingrediente_ing.required' => 'Debe elegir una categoría.',
            'pro_ing.max'      => 'Las proteínas no pueden superar los 100 g por cada 100 g.',
            'car_ing.max'      => 'Los carbohidratos no pueden superar los 100 g por cada 100 g.',
            'gra_ing.max'      => 'Las grasas no pueden superar los 100 g por cada 100 g.',
            'den_ing.gt'       => 'La densidad debe ser mayor que cero.',
            'pre_ing.gt'       => 'El precio debe ser mayor que cero. Déjalo vacío si no lo sabes.',
            'pre_ing.max'      => 'El precio por kilo es demasiado alto.',
        ]);

        // Los tres macronutrientes juntos no pueden pesar mas de 100 g
        // dentro de una porcion de 100 g. Se valida junto al resto para que
        // el mensaje aparezca en el formulario y no en una pantalla de error.
        $validador->after(function ($v) use ($request) {
            $suma = (float) $request->input('pro_ing', 0)
                  + (float) $request->input('car_ing', 0)
                  + (float) $request->input('gra_ing', 0);

            if ($suma > 100) {
                $v->errors()->add('pro_ing', sprintf(
                    'Proteínas, carbohidratos y grasas suman %.1f g; no pueden pasar de 100 g por cada 100 g de alimento.',
                    $suma
                ));
            }
        });

        $datos = $validador->validate();

        $this->comprobarConIa('nom_ing', $datos['nom_ing'], Ingrediente::class, $id,
            'un ingrediente o alimento comestible');

        // Las calorías no se escriben: salen de los macros. Un macro vacío
        // cuenta como cero; si los tres están vacíos, no hay dato.
        $macros = array_map(fn ($clave) => $datos[$clave] ?? null, ['pro_ing', 'car_ing', 'gra_ing']);
        $datos['cal_ing'] = array_filter($macros, fn ($valor) => $valor !== null) === []
            ? null
            : Ingrediente::calcularCalorias(...array_map('floatval', $macros));

        return $datos + [
            'ale_ing' => $request->boolean('ale_ing'),
            'act_ing' => $request->boolean('act_ing', true),
        ];
    }
}
